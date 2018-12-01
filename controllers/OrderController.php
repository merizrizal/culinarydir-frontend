<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;
use yii\web\NotFoundHttpException;

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
    
    public function actionOrderList()
    {
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'transactionItems' => function($query) {
                    
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->asArray()->one();
            
        return $this->render('order_list', [
            'modelTransactionSession'=> $modelTransactionSession,
        ]);
    }
    
    public function actionCheckout($id)
    {
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'business',
                'transactionItems' => function($query) {
                
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.id' => $id])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();
        
        if (!empty($modelTransactionSession)) {
            
            if (Yii::$app->request->post()) {
                
                $modelTransactionSession->is_closed = true;
                
                if ($modelTransactionSession->save()) {
                    
                    $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession['business']['phone3']), 1);
                    
                    $itemCount = count($modelTransactionSession['transactionItems']) - 1;
                    $messageOrder = 'Halo ' . $modelTransactionSession['business']['name'] . ',%0ASaya ' . Yii::$app->user->getIdentity()->full_name . 'via Asikmakan ingin memesan:%0A%0A';
                    
                    foreach ($modelTransactionSession['transactionItems'] as $itemIndex => $dataTransactionItem) {
                        
                        $messageOrder .= $dataTransactionItem['businessProduct']['name'] . ' (Jumlah: ' . $dataTransactionItem['amount'] . ')';
                        $messageOrder .= (!empty($dataTransactionItem['note'])) ? '%0A' . $dataTransactionItem['note'] : '';
                        $messageOrder .= ($itemCount !== $itemIndex) ? '%0A%0A' : '';
                    }
                    
                    $messageOrder = str_replace(' ', '%20', $messageOrder);
                    
                    return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
                } else {
                    
                    Yii::$app->session->setFlash('message', [
                        'title' => 'Gagal Checkout',
                        'message' => 'Terjadi kesalahan saat menyimpan data',
                    ]);
                }
            }
            
            return $this->render('checkout', [
                'modelTransactionSession' => $modelTransactionSession
            ]);
        } else {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}