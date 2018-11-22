<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use core\models\TransactionSession;
use core\models\TransactionItem;


/**
 * OrderAction controller
 */
class OrderActionController extends base\BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'save-order' => ['post'],
                        'change-qty' => ['post'],
                        'remove-item' => ['post'],
                    ],
                ],
            ]);
    }
    
    public function actionSaveOrder() {
        
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;
        
        if (empty($post['sess_id'])) {
            
            $modelTransactionSession = new TransactionSession();
            $modelTransactionSession->customer_name = '';
            $modelTransactionSession->total_price = $post['total_price'];
            $modelTransactionSession->user_ordered = Yii::$app->user->identity->id;
            
            $flag = $modelTransactionSession->save();
        } else {
            
            $modelTransactionSession = TransactionSession::findOne($post['sess_id']);
            $modelTransactionSession->total_price = $post['total_price'];
            
            $flag = $modelTransactionSession->save();
        }
        
        if ($flag) {
            
            $modelTransactionItem = TransactionItem::find()
                ->andWhere(['transaction_session_id' => $modelTransactionSession->id])
                ->andWhere(['business_product_id' => $post['menu_id']])
                ->one();
            
            if (!empty($modelTransactionItem)) {
                
                $modelTransactionItem->amount++;
            } else {
                
                $modelTransactionItem = new TransactionItem();
                $modelTransactionItem->transaction_session_id = $modelTransactionSession->id;
                $modelTransactionItem->business_product_id = $post['menu_id'];
                $modelTransactionItem->price = $post['price'];
                $modelTransactionItem->amount = 1;
            }
            
            $flag = $modelTransactionItem->save();
        }
        
        $return = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $return['success'] = true;
            $return['sess_id'] = $modelTransactionSession->id;
            $return['message']['type'] = 'success';
            $return['message']['icon'] = 'fa fa-check';
            $return['message']['title'] = 'Penambahan menu sukses';
            $return['message']['text'] = '<product> telah ditambahkan ke dalam keranjang';
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penambahan menu gagal';
            $return['message']['text'] = 'Harap cek kembali menu yang Anda tambahkan';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionChangeQty($id) {
        
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $id])
            ->one();
        
        $jumlahPrior = $modelTransactionItem->amount;
        $modelTransactionItem->amount = $post['amount'];
        
        if (($flag = $modelTransactionItem->save())) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_price = $modelTransactionSession->total_price + ($modelTransactionItem->price * ($post['amount'] - $jumlahPrior));
            
            $flag = $modelTransactionSession->save();
        }
        
        $return = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $return['success'] = true;
            $return['subtotal'] = Yii::$app->formatter->asCurrency($modelTransactionItem->amount * $modelTransactionItem->price);
            $return['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penambahan produk gagal';
            $return['message']['text'] = 'Harap cek kembali produk yang Anda tambahkan';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionRemoveItem($id) {
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $id])
            ->one();
        
        if (($flag = $modelTransactionItem->save())) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_price = $modelTransactionSession->total_price - ($modelTransactionItem->price * $modelTransactionItem->amount);
            
            $flag = $modelTransactionSession->save();
        }
        
        $flag = TransactionItem::deleteAll(['id' => $id]);
        
        $return = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $return['success'] = true;
            $return['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penambahan produk gagal';
            $return['message']['text'] = 'Harap cek kembali produk yang Anda tambahkan';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}