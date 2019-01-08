<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;
use core\models\TransactionSessionOrder;
use core\models\DeliveryMethod;
use core\models\PaymentMethod;

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
                'business.businessDeliveries.deliveryMethod',
                'business.businessDeliveries' => function ($query) {
                    
                    $query->andOnCondition(['business_delivery.is_active' => true]);
                },
                'business.businessPayments.paymentMethod',
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
            
        if ($post = Yii::$app->request->post()) {
            
            $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;
            $modelTransactionSessionOrder->delivery_method_id = $post['delivery_method_id'];
            $modelTransactionSessionOrder->payment_method_id = $post['payment_method_id'];
            
            $modelDelivery = DeliveryMethod::findOne(['id' => $post['delivery_method_id']]);
            $modelPayment = PaymentMethod::findOne(['id' => $post['payment_method_id']]);
            
            if ($modelTransactionSessionOrder->save()) {
             
                $modelTransactionSession->is_closed = true;
                
                if ($modelTransactionSession->save()) {
                    
                    $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession['business']['phone3']), 1);
                    
                    $itemCount = count($modelTransactionSession['transactionItems']) - 1;
                    $messageOrder = 'Halo ' . $modelTransactionSession['business']['name'] . ',%0Asaya ' . Yii::$app->user->getIdentity()->full_name . ' (via Asikmakan) ingin memesan:%0A%0A';
                    
                    foreach ($modelTransactionSession['transactionItems'] as $itemIndex => $dataTransactionItem) {
                        
                        $messageOrder .= $dataTransactionItem['amount'] . 'x ' . $dataTransactionItem['businessProduct']['name'] . ' @' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']);
                        $messageOrder .= (!empty($dataTransactionItem['note'])) ? ' ' . $dataTransactionItem['note'] : '';
                        $messageOrder .= ($itemCount !== $itemIndex) ? '%0A%0A' : '';
                    }
                    
                    $messageOrder .= '%0A%0A' . 'Pengiriman dengan ' . $modelDelivery['delivery_name'];
                    
                    $messageOrder .= '%0A%0A' . 'Pembayaran dengan ' . $modelPayment['payment_name'];
                    
                    $messageOrder .= '%0A%0A' . 'Total: ' . Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']);
                    
                    $messageOrder = str_replace(' ', '%20', $messageOrder);
                    
                    return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
                } else {
                    
                    Yii::$app->session->setFlash('message', [
                        'title' => 'Gagal Checkout',
                        'message' => 'Terjadi kesalahan saat menyimpan data',
                    ]);
                }
            } else {
                
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