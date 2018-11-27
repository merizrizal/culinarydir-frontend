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
    
    public function actionSaveOrder()
    {
        $post = Yii::$app->request->post();
        
        $modelTransactionSession = TransactionSession::find()
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $return = [];
        
        if (!empty($modelTransactionSession)) {
            
            $modelTransactionSession->total_price += $post['price'];
        } else {
            
            $modelTransactionSession = new TransactionSession();
            $modelTransactionSession->total_price = $post['price'];
            $modelTransactionSession->user_ordered = Yii::$app->user->identity->id;
            $modelTransactionSession->business_id = $post['business_id'];
        }
        
        if ($modelTransactionSession->business_id == $post['business_id']) {
            
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
        } else {
            
            $transaction->rollBack();
            
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penambahan menu gagal';
            $return['message']['text'] = 'Mohon maaf anda tidak dapat memesan menu dari dua tempat secara bersamaan';
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $return;
        }
        
        if ($flag) {
            
            $transaction->commit();
            
            $return['message']['type'] = 'success';
            $return['message']['icon'] = 'fa fa-check';
            $return['message']['title'] = 'Penambahan menu sukses';
            $return['message']['text'] = '<product> telah ditambahkan ke dalam daftar';
        } else {
            
            $transaction->rollBack();
            
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penambahan menu gagal';
            $return['message']['text'] = 'Terjadi kesalahan saat memesan menu, silahkan ulangi kembali';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionChangeQty($id)
    {
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $id])
            ->one();
        
        $jumlahPrior = $modelTransactionItem->amount;
        $modelTransactionItem->amount = $post['amount'];
        
        if ($modelTransactionItem->save()) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_price += $modelTransactionItem->price * ($post['amount'] - $jumlahPrior);
        }
        
        $return = [];
        
        if ($modelTransactionSession->save()) {
            
            $transaction->commit();
            
            $return['success'] = true;
            $return['subtotal'] = Yii::$app->formatter->asCurrency($modelTransactionItem->amount * $modelTransactionItem->price);
            $return['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Perubahan jumlah menu gagal';
            $return['message']['text'] = 'Terjadi kesalahan saat proses perubahan jumlah menu, silahkan ulangi kembali';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionRemoveItem()
    {
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $post['item_id']])
            ->one();
        
        if (($flag = $modelTransactionItem->save())) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_price -= $modelTransactionItem->price * $modelTransactionItem->amount;
            
            $flag = $modelTransactionSession->save();
        }
        
        if (($flag = TransactionItem::deleteAll(['id' => $post['item_id']]))) {
            
            if ($modelTransactionSession->total_price == 0) {
                
                $flag = TransactionSession::deleteAll(['id' => $modelTransactionSession->id]);
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
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Penghapusan produk gagal';
            $return['message']['text'] = 'Terjadi kesalahan saat proses penghapusan, silahkan ulangi kembali';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionSaveNotes($id)
    {
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => $id])
            ->one();
            
        $modelTransactionItem->note = !empty($post['note']) ? $post['note'] : null;
        
        $return = [];
        
        if ($modelTransactionItem->save()) {
            
            $transaction->commit();
            
            $return['success'] = true;
        } else {
            
            $transaction->rollBack();
            
            $return['success'] = false;
            $return['message']['type'] = 'danger';
            $return['message']['icon'] = 'fa fa-warning';
            $return['message']['title'] = 'Input keterangan gagal';
            $return['message']['text'] = 'Harap input kembali keterangan untuk menu ini.';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}