<?php
namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use sycomponent\Tools;

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
    
    public function actionOrderList() {
        
        $modelTransactionSession = null;
        
        return $this->render('order_list', [
            'modelTransactionSession'=> $modelTransactionSession,
        ]);
    }
}
