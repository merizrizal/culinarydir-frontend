<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use core\models\Business;
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
                        'save-notes' => ['post']
                    ],
                ],
            ]);
    }
    
    public function actionSaveOrder()
    {
        $post = Yii::$app->request->post();
        
        $modelTransactionSession = TransactionSession::find()
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();
        
        if (!empty($modelTransactionSession)) {
            
            $modelTransactionSession->total_price += $post['price'];
            $modelTransactionSession->total_amount++;
        } else {
            
            $modelTransactionSession = new TransactionSession();
            $modelTransactionSession->user_ordered = Yii::$app->user->getIdentity()->id;
            $modelTransactionSession->business_id = $post['business_id'];
            $modelTransactionSession->total_price = $post['price'];
            $modelTransactionSession->total_amount = 1;
        }
        
        $modelBusinessSession = Business::find()
            ->andWhere(['business.id' => $modelTransactionSession->business_id])
            ->one();
        
        $return = [];
            
        if ($modelTransactionSession->business_id == $post['business_id']) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($flag = $modelTransactionSession->save())) {
                
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
            
            if ($flag) {
                
                $transaction->commit();
                
                $return['success'] = true;
                $return['type'] = 'success';
                $return['icon'] = 'aicon aicon-icon-tick-in-circle';
                $return['title'] = 'Penambahan menu sukses';
                $return['text'] = '<product> telah ditambahkan ke dalam daftar';
                $return['total_price'] = $modelTransactionSession->total_price;
                $return['total_amount'] = $modelTransactionSession->total_amount;
                $return['place_name'] = !empty($modelBusinessSession) ? $modelBusinessSession['name'] : '';
            } else {
                
                $transaction->rollBack();
                
                $return['success'] = false;
                $return['type'] = 'danger';
                $return['icon'] = 'aicon aicon-icon-info';
                $return['title'] = 'Penambahan menu gagal';
                $return['text'] = 'Terjadi kesalahan saat memesan menu, silahkan ulangi kembali';
            }
        } else {
            
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Penambahan menu gagal';
            $return['text'] = 'Mohon maaf anda tidak dapat memesan menu dari dua tempat secara bersamaan';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionChangeQty($id)
    {
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
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
            $modelTransactionSession->total_price += $modelTransactionItem->price * ($post['amount'] - $jumlahPrior);
            $modelTransactionSession->total_amount += ($post['amount'] - $jumlahPrior);
            
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
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Perubahan jumlah menu gagal';
            $return['text'] = 'Terjadi kesalahan saat proses perubahan jumlah menu, silahkan ulangi kembali';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionRemoveItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $id])
            ->one();
        
        if (!empty($modelTransactionItem)) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_price -= $modelTransactionItem->price * $modelTransactionItem->amount;
            $modelTransactionSession->total_amount -= $modelTransactionItem->amount;
            
            if (($flag = $modelTransactionSession->save())) {
            
                if (($flag = $modelTransactionItem->delete())) {
                    
                    if ($modelTransactionSession->total_price == 0) {
                        
                        $flag = $modelTransactionSession->delete();
                    }
                }
            }
        }
        
        $return = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $return['success'] = true;
            $return['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Penghapusan produk gagal';
            $return['text'] = 'Terjadi kesalahan saat proses penghapusan, silahkan ulangi kembali';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionSaveNotes($id)
    {
        $post = Yii::$app->request->post();
        
        $modelTransactionItem = TransactionItem::find()
            ->andWhere(['transaction_item.id' => $id])
            ->one();
            
        $modelTransactionItem->note = !empty($post['note']) ? $post['note'] : null;
        
        $return = [];
        
        if ($modelTransactionItem->save()) {
            
            $return['success'] = true;
        } else {
            
            $return['success'] = false;
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Input keterangan gagal';
            $return['text'] = 'Harap input kembali keterangan untuk menu ini.';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}