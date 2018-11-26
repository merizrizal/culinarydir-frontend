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
    
    public function actionCheckout($id)
    {
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'business',
                'transactionItems',
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.id' => $id])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->id])
            ->andWhere(['transaction_session.is_closed' => false])
            ->one();
        
        if (!empty($modelTransactionSession)) {
            
            if (Yii::$app->request->post()) {
                
//                 echo '<pre>'; print_r(Yii::$app->request->post()); exit();
                
//                 $modelTransactionSession->is_closed = true;
                
//                 if ($modelTransactionSession->save()) {
                    
//                     $businessPhone = $modelTransactionSession->business->phone1;
//                     $messageOrder = Yii::$app->request->post('message');
                    
//                     return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=%20' . $messageOrder);
//                 } else {
                    
//                 }
            }
            
            return $this->render('checkout', [
                'modelTransactionSession' => $modelTransactionSession
            ]);
        } else {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
