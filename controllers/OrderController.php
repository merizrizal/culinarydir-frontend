<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;
use core\models\TransactionSessionOrder;

/**
 * Order controller
 */
class OrderController extends base\BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        
                    ],
                ],
            ]);
    }
    
    public function actionCheckout()
    {
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'business',
                'business.businessDeliveries' => function ($query) {
                    
                    $query->andOnCondition(['business_delivery.is_active' => true]);
                },
                'business.businessPayments' => function ($query) {
                
                    $query->andOnCondition(['business_payment.is_active' => true]);
                },
                'transactionItems' => function ($query) {
                
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();
                
        $modelTransactionSessionOrder = new TransactionSessionOrder();
                
        if (($post = Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;
            $modelTransactionSessionOrder->business_delivery_id = $post['business_delivery_id'];
            $modelTransactionSessionOrder->business_payment_id = $post['business_payment_id'];
            
            $modelTransactionSession->is_closed = true;
            $modelTransactionSession->note = !empty($post['TransactionSession']['note']) ? $post['TransactionSession']['note'] : null;
            
            if (($flag = $modelTransactionSessionOrder->save() && $modelTransactionSession->save())) {
                
                $dataDelivery = [];
                
                foreach ($modelTransactionSession['business']['businessDeliveries'] as $dataBusinessDelivery) {
                    
                    if ($dataBusinessDelivery['id'] == $post['business_delivery_id']) {
                        
                        $dataDelivery = $dataBusinessDelivery;
                        break;
                    }
                }
                
                $dataPayment = [];
                
                foreach ($modelTransactionSession['business']['businessPayments'] as $dataBusinessPayment) {
                    
                    if ($dataBusinessPayment['id'] == $post['business_payment_id']) {
                        
                        $dataPayment = $dataBusinessPayment;
                        break;
                    }
                }
                    
                $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession['business']['phone3']), 1);
                
                $messageOrder = 'Halo ' . $modelTransactionSession['business']['name'] . ',\nsaya ' . Yii::$app->user->getIdentity()->full_name . ' (via Asikmakan) ingin memesan:\n\n';
                
                foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem) {
                    
                    $messageOrder .= $dataTransactionItem['amount'] . 'x ' . $dataTransactionItem['businessProduct']['name'] . ' @' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']);
                    $messageOrder .= (!empty($dataTransactionItem['note']) ? '\n' . $dataTransactionItem['note'] : '') . '\n\n';
                }
                
                $messageOrder .= '*Total: ' . Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) . '*';
                $messageOrder .= !empty($dataDelivery['note']) ? '\n\n' . $dataDelivery['note'] : '';
                $messageOrder .= !empty($dataPayment['note']) ? '\n\n' . $dataPayment['note'] : '';
                $messageOrder .= !empty($modelTransactionSession['note']) ? '\n\nCatatan: ' . $modelTransactionSession['note'] : '';
                
                $messageOrder = str_replace('\n', '%0A', str_replace(' ', '%20', $messageOrder));
            }
            
            if ($flag) {
                
                $transaction->commit();
                
                return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
            } else {
                
                $transaction->rollBack();
                
                Yii::$app->session->setFlash('message', [
                    'title' => 'Gagal Checkout',
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ]);
            }
        }
        
        return $this->render('checkout', [
            'modelTransactionSession' => $modelTransactionSession,
            'modelTransactionSessionOrder' => $modelTransactionSessionOrder
        ]);
    }
}