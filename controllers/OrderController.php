<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;
use core\models\TransactionSessionOrder;
use core\models\BusinessDelivery;
use core\models\BusinessPayment;

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

            $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;
            $modelTransactionSessionOrder->delivery_method_id = $post['delivery_method_id'];
            $modelTransactionSessionOrder->payment_method_id = $post['payment_method_id'];
            
            $modelTransactionSession->is_closed = true;
            $modelTransactionSession->note = !empty($post['TransactionSession']['note']) ? $post['TransactionSession']['note'] : null;
            
            if (($flag = $modelTransactionSessionOrder->save() && $modelTransactionSession->save())) {
            
                $modelBusinessDelivery = BusinessDelivery::find()
                    ->joinWith(['deliveryMethod'])
                    ->andWhere(['business_delivery.business_id' => $modelTransactionSession['business']['id']])
                    ->andWhere(['business_delivery.delivery_method_id' => $post['delivery_method_id']])
                    ->asArray()->one();
                
                $modelBusinessPayment = BusinessPayment::find()
                    ->joinWith(['paymentMethod'])
                    ->andWhere(['business_payment.business_id' => $modelTransactionSession['business']['id']])
                    ->andWhere(['business_payment.payment_method_id' => $post['payment_method_id']])
                    ->asArray()->one();
                    
                $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession['business']['phone3']), 1);
                
                $messageOrder = 'Halo ' . $modelTransactionSession['business']['name'] . ',\nsaya ' . Yii::$app->user->getIdentity()->full_name . ' (via Asikmakan) ingin memesan:\n\n';
                
                foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem) {
                    
                    $messageOrder .= $dataTransactionItem['amount'] . 'x ' . $dataTransactionItem['businessProduct']['name'] . ' @' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']);
                    $messageOrder .= (!empty($dataTransactionItem['note']) ? '\n' . $dataTransactionItem['note'] : '') . '\n\n';
                }
                
                $messageOrder .= 'Total: ' . Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']);

                $messageOrder .= '\n\nPengiriman dengan ' . $modelBusinessDelivery['deliveryMethod']['delivery_name'];
                $messageOrder .= !empty($modelBusinessDelivery['note']) ? '\n' . $modelBusinessDelivery['note'] : '';
        
                $messageOrder .= '\n\nPembayaran dengan ' . $modelBusinessPayment['paymentMethod']['payment_name'];
                $messageOrder .= !empty($modelBusinessPayment['note']) ? '\n' . $modelBusinessPayment['note'] : '';
                
                $messageOrder .= !empty($modelTransactionSession['note']) ? '\n\nCatatan: ' . $modelTransactionSession['note'] : '';
                
                $messageOrder = str_replace('%5Cn', '%0A', urlencode($messageOrder));
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