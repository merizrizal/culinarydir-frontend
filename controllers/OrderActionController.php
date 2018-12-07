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
                        'save-notes' => ['post']
                    ],
                ],
            ]);
    }
    
    public function actionSaveOrder()
    {
        $post = Yii::$app->request->post();
        
        $modelTransactionSession = TransactionSession::find()
            ->joinWith(['business'])
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
                $return['title'] = 'Penambahan pesanan sukses';
                $return['text'] = '<product> telah ditambahkan ke dalam daftar pesanan';
                $return['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
                $return['total_amount'] = $modelTransactionSession->total_amount;
                $return['place_name'] = $modelTransactionSession['business']['name'];
            } else {
                
                $transaction->rollBack();
                
                $return['success'] = false;
                $return['type'] = 'danger';
                $return['icon'] = 'aicon aicon-icon-info';
                $return['title'] = 'Penambahan pesanan gagal';
                $return['text'] = 'Terjadi kesalahan saat menambahkan pesanan, silahkan ulangi kembali';
            }
        } else {
            
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Penambahan pesanan gagal';
            $return['text'] = 'Mohon maaf anda tidak dapat memesan dari dua tempat secara bersamaan';
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
        
        $amountPrior = $modelTransactionItem->amount;
        $modelTransactionItem->amount = $post['amount'];
        $totalAmount = $post['amount'] - $amountPrior;
        
        if (($flag = $modelTransactionItem->save())) {
            
            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_amount += $totalAmount;
            $modelTransactionSession->total_price += $modelTransactionItem->price * $totalAmount;
            
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
            $return['title'] = 'Perubahan jumlah pesanan gagal';
            $return['text'] = 'Terjadi kesalahan saat proses perubahan jumlah pesanan, silahkan ulangi kembali';
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
            
            if ($modelTransactionSession->total_price == 0) {
                
                $flag = $modelTransactionItem->delete() && $modelTransactionSession->delete();
            } else {
                
                $flag = $modelTransactionItem->delete() && $modelTransactionSession->save();
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
            $return['title'] = 'Penghapusan pesanan gagal';
            $return['text'] = 'Terjadi kesalahan saat menghapus pesanan, silahkan ulangi kembali';
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
            $return['title'] = 'Input keterangan pesanan gagal';
            $return['text'] = 'Harap input kembali keterangan untuk pesanan ini.';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}