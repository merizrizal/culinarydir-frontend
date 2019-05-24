<?php

namespace frontend\controllers;

use core\models\PromoItem;
use core\models\TransactionSession;
use core\models\TransactionSessionOrder;
use yii\filters\VerbFilter;

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

                    $query->orderBy(['transaction_item.created_at' => SORT_ASC]);
                },
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.user_ordered' => \Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();

        $modelTransactionSessionOrder = new TransactionSessionOrder();

        if (($post = \Yii::$app->request->post())) {

            if ($modelTransactionSession->load($post) && $modelTransactionSessionOrder->load($post)) {

                $transaction = \Yii::$app->db->beginTransaction();
                $flag = false;

                $modelTransactionSessionOrder->transaction_session_id = $modelTransactionSession->id;

                if (($flag = $modelTransactionSessionOrder->save())) {

                    $modelTransactionSession->is_closed = true;
                    $modelTransactionSession->promo_item_id = !empty($modelTransactionSession->promo_item_id) ? $modelTransactionSession->promo_item_id : null;

                    if (!empty($post['TransactionSession']['promo_item_id'])) {

                        $modelPromoItem = PromoItem::find()
                            ->joinWith(['userPromoItem'])
                            ->andWhere(['promo_item.id' => $post['TransactionSession']['promo_item_id']])
                            ->andWhere(['promo_item.business_claimed' => null])
                            ->andWhere(['promo_item.not_active' => false])
                            ->andWhere(['user_promo_item.user_id' => \Yii::$app->user->getIdentity()->id])
                            ->one();

                        $modelPromoItem->business_claimed = $modelTransactionSession->business_id;
                        $modelPromoItem->not_active = true;

                        if (($flag = $modelPromoItem->save())) {

                            $modelTransactionSession->promo_item_id = $modelPromoItem->id;
                            $modelTransactionSession->discount_value = $modelPromoItem->amount;
                            $modelTransactionSession->discount_type = 'Amount';
                        }
                    }

                    $flag = $modelTransactionSession->save();
                }

                if ($flag) {

                    $dataDelivery = [];
                    $dataPayment = [];

                    foreach ($modelTransactionSession['business']['businessDeliveries'] as $dataBusinessDelivery) {

                        if ($dataBusinessDelivery['id'] == $modelTransactionSessionOrder->business_delivery_id) {

                            $dataDelivery = $dataBusinessDelivery;
                            break;
                        }
                    }

                    foreach ($modelTransactionSession['business']['businessPayments'] as $dataBusinessPayment) {

                        if ($dataBusinessPayment['id'] == $modelTransactionSessionOrder->business_payment_id) {

                            $dataPayment = $dataBusinessPayment;
                            break;
                        }
                    }

                    $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession['business']['phone3']), 1);

                    $messageOrder = 'Halo ' . $modelTransactionSession['business']['name'] . ',\nsaya ' . \Yii::$app->user->getIdentity()->full_name . ' (via Asikmakan) ingin memesan:\n\n';

                    foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem) {

                        $messageOrder .= $dataTransactionItem['amount'] . 'x ' . $dataTransactionItem['businessProduct']['name'] . ' @' . \Yii::$app->formatter->asCurrency($dataTransactionItem['price']);
                        $messageOrder .= (!empty($dataTransactionItem['note']) ? '\n' . $dataTransactionItem['note'] : '') . '\n\n';
                    }

                    $messageOrder .= '*Subtotal: ' . \Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) . '*';

                    if (!empty($modelTransactionSession['discount_value'])) {

                        $subtotal = $modelTransactionSession['total_price'] - $modelTransactionSession['discount_value'];
                        $messageOrder .= '\n\n*Promo: ' . \Yii::$app->formatter->asCurrency($modelTransactionSession['discount_value']) . '*';
                        $messageOrder .= '\n\n*Grand Total: ' . \Yii::$app->formatter->asCurrency($subtotal < 0 ? 0 : $subtotal) . '*';
                    }

                    $messageOrder .= !empty($dataDelivery['note']) ? '\n\n' . $dataDelivery['note'] : '';
                    $messageOrder .= !empty($dataPayment['note']) ? '\n\n' . $dataPayment['note'] : '';
                    $messageOrder .= !empty($modelTransactionSession['note']) ? '\n\nCatatan: ' . $modelTransactionSession['note'] : '';

                    $messageOrder = str_replace('%5Cn', '%0A', str_replace('+', '%20', urlencode($messageOrder)));
                }

                if ($flag) {

                    $transaction->commit();

                    return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
                } else {

                    \Yii::$app->session->setFlash('message', [
                        'title' => 'Gagal Checkout',
                        'message' => 'Terjadi kesalahan saat menyimpan data',
                    ]);

                    $transaction->rollBack();
                }
            }
        }

        \Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $promoItemClaimed = PromoItem::find()
            ->joinWith([
                'userPromoItem',
                'promo'
            ])
            ->andWhere(['promo_item.not_active' => false])
            ->andWhere(['promo_item.business_claimed' => null])
            ->andWhere(['>=', 'promo.date_end', \Yii::$app->formatter->asDate(time())])
            ->andWhere(['promo.not_active' => false])
            ->andWhere(['user_promo_item.user_id' => \Yii::$app->user->getIdentity()->id])
            ->asArray()->all();

        \Yii::$app->formatter->timeZone = 'UTC';

        $dataOption = [];

        foreach ($promoItemClaimed as $promoItem) {

            $dataOption[$promoItem['id']] = ['data-amount' => $promoItem['promo']['amount'], 'data-minimum-order' => $promoItem['promo']['minimum_amount_order']];
        }

        return $this->render('checkout', [
            'modelTransactionSession' => $modelTransactionSession,
            'modelTransactionSessionOrder' => $modelTransactionSessionOrder,
            'promoItemClaimed' => $promoItemClaimed,
            'dataOption' => $dataOption
        ]);
    }
}