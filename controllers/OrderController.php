<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use core\models\TransactionSession;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                
                $modelTransactionSession->is_closed = true;
                
                if ($modelTransactionSession->save()) {
                    
                    $businessPhone = '62' . substr(str_replace('-', '', $modelTransactionSession->business->phone1), 1);
                    $messageOrder = Yii::$app->request->post('message');
                    
                    return $this->redirect('https://api.whatsapp.com/send?phone=' . $businessPhone . '&text=' . $messageOrder);
                } else {
                    
                    $result = [];
                    $result['success'] = false;
                    $result['icon'] = 'aicon aicon-icon-info';
                    $result['title'] = 'Gagal Checkout';
                    $result['message'] = 'Terjadi kesalahan ketika menyimpan data';
                    $result['type'] = 'danger';
                    
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $result;
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
