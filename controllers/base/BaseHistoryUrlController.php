<?php
namespace frontend\controllers\base;

use Yii;

class BaseHistoryUrlController extends BaseController
{
    public function beforeAction($action) {

        Yii::$app->user->setReturnUrl(Yii::$app->request->url);

        return parent::beforeAction($action);
    }
}
