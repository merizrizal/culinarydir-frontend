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
use core\models\TransactionSessionDelivery;

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
                
                $result = [];

                $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;
                $modelTransactionSessionOrder->business_delivery_id = !empty($post['business_delivery_id']) ? $post['business_delivery_id'] : null;
                $modelTransactionSessionOrder->business_payment_id = !empty($post['business_payment_id']) ? $post['business_payment_id'] : null;

                $modelTransactionSession->status = 'New';
                $modelTransactionSession->note = !empty($post['TransactionSession']['note']) ? $post['TransactionSession']['note'] : null;

                if (($flag = ($modelTransactionSessionOrder->save() && $modelTransactionSession->save()))) {
                    
                    $faker = Factory::create();
                    
                    $modelTransactionSessionDelivery = new TransactionSessionDelivery();
                    $modelTransactionSessionDelivery->transaction_session_id = $modelTransactionSession->id;
                    $modelTransactionSessionDelivery->driver_id = Yii::$app->user->getIdentity()->id;
                    $modelTransactionSessionDelivery->total_distance = $faker->randomNumber(2);
                    $modelTransactionSessionDelivery->total_delivery_fee = $faker->randomNumber(6);
                    
                    if (($flag = $modelTransactionSessionDelivery->save())) {
                            
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
                        
                        $result['detail'] = [];
    
                        foreach ($modelTransactionSession['transactionItems'] as $i => $dataTransactionItem) {
    
                            $messageOrder .= $dataTransactionItem['amount'] . 'x ' . $dataTransactionItem['businessProduct']['name'] . ' @' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']);
                            $messageOrder .= (!empty($dataTransactionItem['note']) ? '\n' . $dataTransactionItem['note'] : '') . '\n\n';
                            
                            $result['detail'][$i] = [];
                            $result['detail'][$i]['menu'] = $dataTransactionItem['businessProduct']['name'];
                            $result['detail'][$i]['price'] = $dataTransactionItem['price'];
                            $result['detail'][$i]['amount'] = $dataTransactionItem['amount'];
                            $result['detail'][$i]['note'] = $dataTransactionItem['note'];
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
            }
            
            if ($flag) {

                $transaction->commit();
                
                $result['header'] = [];
                $result['header']['customer_id'] = $modelTransactionSession['user_ordered'];
                $result['header']['customer_name'] = $modelTransactionSession['userOrdered']['full_name'];
                $result['header']['customer_username'] = $modelTransactionSession['userOrdered']['username'];
                $result['header']['customer_phone'] = $modelTransactionSession['userOrdered']['userPerson']['person']['phone'];
                $result['header']['customer_location'] = $modelTransactionSession['business']['businessLocation']['coordinate'];
                $result['header']['customer_address'] = $modelTransactionSession['userOrdered']['userPerson']['person']['address'];
                
                $result['header']['business_id'] = $modelTransactionSession['business_id'];
                $result['header']['business_name'] = $modelTransactionSession['business']['name'];
                $result['header']['business_phone'] = $modelTransactionSession['business']['phone3'];
                $result['header']['business_location'] = $modelTransactionSession['business']['businessLocation']['coordinate'];
                $result['header']['business_address'] =
                    AddressType::widget([
                        'businessLocation' => $modelTransactionSession['business']['businessLocation'],
                        'showDetail' => false
                    ]);
                    
                $result['header']['order_id'] = substr($modelTransactionSession['order_id'], 0, 6);
                $result['header']['note'] = $modelTransactionSession['note'];
                $result['header']['total_price'] = $modelTransactionSession['total_price'];
                $result['header']['total_amount'] = $modelTransactionSession['total_amount'];
                $result['header']['total_distance'] = $modelTransactionSessionDelivery['total_distance'];
                $result['header']['total_delivery_fee'] = $modelTransactionSessionDelivery['total_delivery_fee'];
                $result['header']['order_status'] = $modelTransactionSession['status'];
                
                $client = new Client(new Version2X(Yii::$app->params['socketIO']));
                
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