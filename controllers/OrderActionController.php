<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use core\models\TransactionSession;
use core\models\TransactionItem;
use yii\web\NotFoundHttpException;
use core\models\PromoItem;

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
                        'save-notes' => ['post'],
                        'redeem-promo' => ['post']
                    ],
                ],
            ]);
    }

    public function actionSaveOrder()
    {
        $post = Yii::$app->request->post();

        $modelTransactionSession = TransactionSession::find()
            ->andWhere(['user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['is_closed' => false])
            ->one();

        if (!empty($modelTransactionSession)) {

            $modelTransactionSession->total_price += $post['product_price'];
            $modelTransactionSession->total_amount++;
        } else {

            $modelTransactionSession = new TransactionSession();
            $modelTransactionSession->user_ordered = Yii::$app->user->getIdentity()->id;
            $modelTransactionSession->business_id = $post['business_id'];
            $modelTransactionSession->total_price = $post['product_price'];
            $modelTransactionSession->total_amount = 1;
        }

        $result = [];

        if ($modelTransactionSession->business_id == $post['business_id']) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            if (($flag = $modelTransactionSession->save())) {

                $modelTransactionItem = TransactionItem::find()
                    ->andWhere(['transaction_session_id' => $modelTransactionSession->id])
                    ->andWhere(['business_product_id' => $post['product_id']])
                    ->one();

                $modelTransactionItem = new TransactionItem();
                $modelTransactionItem->transaction_session_id = $modelTransactionSession->id;
                $modelTransactionItem->business_product_id = $post['product_id'];
                $modelTransactionItem->price = $post['product_price'];
                $modelTransactionItem->amount = 1;

                $flag = $modelTransactionItem->save();
            }

            if ($flag) {

                $transaction->commit();

                $result['success'] = true;
                $result['item_id'] = $modelTransactionItem->id;
                $result['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
                $result['total_amount'] = $modelTransactionSession->total_amount;
            } else {

                $transaction->rollBack();

                $result['success'] = false;
                $result['type'] = 'danger';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Penambahan pesanan gagal';
                $result['text'] = 'Terjadi kesalahan saat menambahkan pesanan, silahkan pesan kembali';
            }
        } else {

            $result['success'] = false;
            $result['type'] = 'danger';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Penambahan pesanan gagal';
            $result['text'] = 'Mohon maaf anda tidak dapat memesan dari dua tempat secara bersamaan';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionChangeQty()
    {
        $post = Yii::$app->request->post();

        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => !empty($post['id']) ? $post['id'] : null])
            ->one();

        if (empty($modelTransactionItem)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;

        $amountPrior = $modelTransactionItem->amount;
        $modelTransactionItem->amount = $post['amount'];
        $totalAmount = $post['amount'] - $amountPrior;

        if (($flag = $modelTransactionItem->save())) {

            $modelTransactionSession = $modelTransactionItem->transactionSession;
            $modelTransactionSession->total_amount += $totalAmount;
            $modelTransactionSession->total_price += $modelTransactionItem->price * $totalAmount;

            $flag = $modelTransactionSession->save();
        }

        $result = [];

        if ($flag) {

            $transaction->commit();

            $result['success'] = true;
            $result['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
            $result['total_amount'] = $modelTransactionSession->total_amount;
        } else {

            $transaction->rollBack();

            $result['success'] = false;
            $result['type'] = 'danger';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Perubahan jumlah pesanan gagal';
            $result['text'] = 'Terjadi kesalahan saat proses perubahan jumlah pesanan, silahkan ulangi kembali';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionRemoveItem()
    {
        $modelTransactionItem = TransactionItem::find()
            ->joinWith([
                'transactionSession'
            ])
            ->andWhere(['transaction_item.id' => !empty(Yii::$app->request->post('id')) ? Yii::$app->request->post('id') : null])
            ->one();

        if (empty($modelTransactionItem)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;

        $modelTransactionSession = $modelTransactionItem->transactionSession;
        $modelTransactionSession->total_price -= $modelTransactionItem->price * $modelTransactionItem->amount;
        $modelTransactionSession->total_amount -= $modelTransactionItem->amount;

        if ($modelTransactionSession->total_price == 0) {

            $flag = $modelTransactionItem->delete() && $modelTransactionSession->delete();
        } else {

            $flag = $modelTransactionItem->delete() && $modelTransactionSession->save();
        }

        $result = [];

        if ($flag) {

            $transaction->commit();

            $result['success'] = true;
            $result['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
            $result['total_amount'] = $modelTransactionSession->total_amount;
        } else {

            $transaction->rollBack();

            $result['success'] = false;
            $result['type'] = 'danger';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Penghapusan pesanan gagal';
            $result['text'] = 'Terjadi kesalahan saat menghapus pesanan, silahkan ulangi kembali';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionSaveNotes()
    {
        $post = Yii::$app->request->post();

        $modelTransactionItem = TransactionItem::find()
            ->andWhere(['transaction_item.id' => !empty($post['id']) ? $post['id'] : null])
            ->one();

        if (empty($modelTransactionItem)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $modelTransactionItem->note = !empty($post['note']) ? $post['note'] : null;

        $result = [];

        if ($modelTransactionItem->save()) {

            $result['success'] = true;
        } else {

            $result['success'] = false;
            $result['type'] = 'danger';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Input keterangan pesanan gagal';
            $result['text'] = 'Harap input kembali keterangan untuk pesanan ini.';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionRedeemPromo()
    {
        $post = Yii::$app->request->post();

        $modelTransactionSession = TransactionSession::find()
            ->joinWith(['transactionItems'])
            ->andWhere(['user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['is_closed' => false])
            ->one();

        $modelPromoItem = PromoItem::find()
            ->andWhere(['SUBSTRING(id, 1, 6)' => trim($post['promo_code'])])
            ->andWhere(['user_claimed' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['not_active' => false])
            ->asArray()->one();

        $result = [];

        if (!empty($post['promo_code'])) {

            $result['empty'] = false;

            $result['success'] = false;
            $result['type'] = 'danger';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Redeem Promo Gagal';
            $result['text'] = 'Proses redeem promo gagal, silahkan ulangi kembali.';

            if (!empty($modelPromoItem)) {

                if (empty($modelTransactionSession->promo_item_id)) {

                    $modelTransactionSession->total_price -= $modelPromoItem['amount'];
                    $modelTransactionSession->promo_item_id = $modelPromoItem['id'];

                    if ($modelTransactionSession->save()) {

                        $result['success'] = true;
                        $result['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price < 0 ? 0 : $modelTransactionSession->total_price);
                        $result['promo_amount'] = Yii::$app->formatter->asCurrency($modelPromoItem['amount']);
                        $result['real_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price + $modelPromoItem['amount']);
                        $result['type'] = 'success';
                        $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                        $result['title'] = 'Redeem Promo Berhasil';
                        $result['text'] = 'Total pembelian akan dikurangi sebesar ' . Yii::$app->formatter->asCurrency($modelPromoItem['amount']);
                    }
                } else {

                    if ($modelTransactionSession->promo_item_id != $modelPromoItem['id']) {

                        $result['text'] = 'Pembelian anda sudah menggunakan promo.';
                    } else {

                        $result['empty'] = true;
                    }
                }
            } else {

                $result['text'] = 'Kode promo yang anda masukkan salah atau tidak valid.';
            }
        } else {

            $result['empty'] = true;
            $result['success'] = true;

            if (!empty($modelTransactionSession->promo_item_id)) {

                $modelTransactionSession->total_price += $modelTransactionSession->promoItem->amount;
                $modelTransactionSession->promo_item_id = null;

                if ($modelTransactionSession->save()) {

                    $result['total_price'] = Yii::$app->formatter->asCurrency($modelTransactionSession->total_price);
                } else {

                    $result['success'] = false;
                    $result['type'] = 'danger';
                    $result['icon'] = 'aicon aicon-icon-info';
                    $result['title'] = 'Hapus Kode Promo Gagal';
                    $result['text'] = 'Proses hapus kode promo gagal, silahkan ulangi kembali.';
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}