<?php
namespace frontend\controllers;

use yii\filters\VerbFilter;

/**
 * Redirect controller
 */
class RedirectController extends base\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'write-review' => ['POST'],
                        'add-photo' => ['POST'],
                    ],
                ],
            ]);
    }

    public function actionWriteReview()
    {

    }

    public function actionAddPhoto()
    {

    }
}