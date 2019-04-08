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
                'userOrdered',
                'userOrdered.userPerson.person'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();

        $modelTransactionSessionOrder = new TransactionSessionOrder();

        if (($post = Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;

            if (!empty($post['TransactionSession']['promo_item_id'])) {

                $modelPromoItem = PromoItem::find()
                    ->joinWith(['userPromoItems'])
                    ->andWhere(['SUBSTRING(promo_item.id, 1, 6)' => trim($post['TransactionSession']['promo_item_id'])])
                    ->andWhere(['promo_item.business_claimed' => null])
                    ->andWhere(['promo_item.not_active' => false])
                    ->andWhere(['user_promo_item.user_id' => Yii::$app->user->getIdentity()->id])
                    ->one();

                if (!empty($modelPromoItem)) {

                    $modelPromoItem->business_claimed = $modelTransactionSession->business_id;
                    $modelPromoItem->not_active = true;

                    if (($flag = $modelPromoItem->save())) {
                        
                        $modelTransactionSession->promo_item_id = $modelPromoItem->id;
                        $modelTransactionSession->discount_value = $modelPromoItem->amount;
                        $modelTransactionSession->discount_type = 'Amount';
                    }
                } else {
                    
                    Yii::$app->session->setFlash('message', [
                        'title' => 'Gagal Checkout',
                        'message' => 'Kode Promo tidak valid',
                    ]);
                    
                    return $this->redirect(['order/checkout']);
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
                
                $result = [];
                
                $result['customer_id'] = $modelTransactionSession['user_ordered'];
                $result['customer_name'] = $modelTransactionSession['userOrdered']['full_name'];
                $result['customer_username'] = $modelTransactionSession['userOrdered']['username'];
                $result['customer_phone'] = $modelTransactionSession['userOrdered']['userPerson']['person']['phone'];
                $result['customer_address'] = $modelTransactionSession['userOrdered']['userPerson']['person']['address'];
                
                $result['business_id'] = $modelTransactionSession['business_id'];
                $result['business_name'] = $modelTransactionSession['business']['name'];
                $result['business_phone'] = $modelTransactionSession['business']['phone3'];
                $result['business_location'] = $modelTransactionSession['business']['businessLocation']['coordinate'];
                $result['business_address'] =
                    AddressType::widget([
                        'businessLocation' => $modelTransactionSession['business']['businessLocation'],
                        'showDetail' => false
                    ]);
                
                $result['order_id'] = substr($modelTransactionSession['order_id'], 0, 6);
                $result['note'] = $modelTransactionSession['note'];
                $result['total_price'] = $modelTransactionSession['total_price'];
                $result['total_amount'] = $modelTransactionSession['total_amount'];
                $result['total_distance'] = $modelTransactionSession['total_distance'];
                $result['total_delivery_fee'] = $modelTransactionSession['total_delivery_fee'];
                $result['order_status'] = $modelTransactionSession['order_status'];
                
                print_r($result); exit;
                
                $client = new Client(new Version2X('http://192.168.0.23:3000'));
                
                $client->initialize();
                $client->emit('broadcast', $result);
                $client->close();

                return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
            } else {

                $transaction->rollBack();

                Yii::$app->session->setFlash('message', [
                    'title' => 'Gagal Checkout',
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ]);

                return $this->redirect(['order/checkout']);
            }
        }

        return $this->render('checkout', [
            'modelTransactionSession' => $modelTransactionSession,
            'modelTransactionSessionOrder' => $modelTransactionSessionOrder
        ]);
    }
}