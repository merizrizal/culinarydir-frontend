<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\PromoItem;
use core\models\TransactionSession;
use core\models\TransactionSessionOrder;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use frontend\components\AddressType;
use Faker\Factory;

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
                'business.businessLocation',
                'business.businessDeliveries' => function ($query) {

                    $query->andOnCondition(['business_delivery.is_active' => true]);
                },
                'business.businessPayments' => function ($query) {

                    $query->andOnCondition(['business_payment.is_active' => true]);
                },
                'transactionItems' => function ($query) {

                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'transactionItems.businessProduct',
                'transactionSessionDelivery',
                'userOrdered',
                'userOrdered.userPerson.person'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.status' => 'Open'])
            ->one();

        $modelTransactionSessionOrder = new TransactionSessionOrder();

        if (($post = Yii::$app->request->post())) {
            
            if ($modelTransactionSession->load($post) && $modelTransactionSessionOrder->load($post)) {
            
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
    
                if (!empty($post['TransactionSession']['promo_item_id'])) {
    
                    $modelPromoItem = PromoItem::find()
                        ->joinWith(['userPromoItem'])
                        ->andWhere(['SUBSTRING(promo_item.id, 1, 6)' => trim($post['TransactionSession']['promo_item_id'])])
                        ->andWhere(['promo_item.business_claimed' => null])
                        ->andWhere(['promo_item.not_active' => false])
                        ->andWhere(['user_promo_item.user_id' => Yii::$app->user->getIdentity()->id])
                        ->one();
    
                    if (!empty($modelPromoItem)) {
    
                        $modelPromoItem->business_claimed = $modelTransactionSession->business_id;
                        $modelPromoItem->not_active = true;
    
                        if ($modelPromoItem->promo->minimum_amount_order <= $modelTransactionSession['total_price']) {
                            
                            if (($flag = $modelPromoItem->save())) {
                                
                                $modelTransactionSession->promo_item_id = $modelPromoItem->id;
                                $modelTransactionSession->discount_value = $modelPromoItem->amount;
                                $modelTransactionSession->discount_type = 'Amount';
                            }
                        } else {
                            
                            Yii::$app->session->setFlash('message', [
                                'title' => 'Gagal Checkout',
                                'message' => 'Minimal memesan sebesar ' .  Yii::$app->formatter->asCurrency($modelPromoItem->promo->minimum_amount_order) . ' untuk mendapatkan promo',
                            ]);
                            
                            $flag = false;
                        }
                    } else {
                        
                        Yii::$app->session->setFlash('message', [
                            'title' => 'Gagal Checkout',
                            'message' => 'Kode Promo tidak valid',
                        ]);
                        
                        $flag = false;
                    }
                }
    
                if ($flag) {
    
                    $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;
                    $modelTransactionSessionOrder->business_delivery_id = !empty($post['business_delivery_id']) ? $post['business_delivery_id'] : null;
                    $modelTransactionSessionOrder->business_payment_id = !empty($post['business_payment_id']) ? $post['business_payment_id'] : null;
    
                    $modelTransactionSession->is_closed = true;
                    $modelTransactionSession->note = !empty($post['TransactionSession']['note']) ? $post['TransactionSession']['note'] : null;
    
                    if (($flag = ($modelTransactionSessionOrder->save() && $modelTransactionSession->save()))) {
    
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
                        
                        if (!empty($modelTransactionSession['discount_value'])) {
                            
                            $subtotal = $modelTransactionSession['total_price'] - $modelTransactionSession['discount_value'];
                            $messageOrder .= '\n\n*Promo: ' . Yii::$app->formatter->asCurrency($modelTransactionSession['discount_value']) . '*';
                            $messageOrder .= '\n\n*Subtotal: ' . Yii::$app->formatter->asCurrency($subtotal < 0 ? 0 : $subtotal) . '*';
                        }
                        
                        $messageOrder .= !empty($dataDelivery['note']) ? '\n\n' . $dataDelivery['note'] : '';
                        $messageOrder .= !empty($dataPayment['note']) ? '\n\n' . $dataPayment['note'] : '';
                        $messageOrder .= !empty($modelTransactionSession['note']) ? '\n\nCatatan: ' . $modelTransactionSession['note'] : '';
    
                        $messageOrder = str_replace('%5Cn', '%0A', str_replace('+', '%20', urlencode($messageOrder)));
                    }
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
        }

        return $this->render('checkout', [
            'modelTransactionSession' => $modelTransactionSession,
            'modelTransactionSessionOrder' => $modelTransactionSessionOrder
        ]);
    }
}