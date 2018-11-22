<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;

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
                'transactionItems',
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->asArray()->one();
            
        return $this->render('order_list', [
            'modelTransactionSession'=> $modelTransactionSession,
        ]);
    }
    
    public function actionCheckout()
    {
        return $this->render('checkout');
    }
}
