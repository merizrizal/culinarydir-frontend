<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\UserRegister;
use backend\models\Person;
use backend\models\UserPerson;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class SiteController extends base\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'zero',
            ],
            'maintenance' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'zero',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionRegister()
    {
        $modelUserRegister = new UserRegister();
        $modelPerson = new Person();

        if (Yii::$app->request->isAjax && $modelUserRegister->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelUserRegister);
        }

        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            $modelPerson->first_name = $post['Person']['first_name'];
            $modelPerson->last_name = $post['Person']['last_name'];
            $modelPerson->email = $post['UserRegister']['email'];
            $modelPerson->phone = $post['Person']['phone'];
            $modelPerson->city_id = $post['Person']['city_id'];

            $flag = $modelPerson->save();

            if ($flag) {

                $modelUserRegister->user_level_id = 4;
                $modelUserRegister->email = $post['UserRegister']['email'];
                $modelUserRegister->username = $post['UserRegister']['username'];
                $modelUserRegister->full_name = $post['Person']['first_name'] . ' ' . $post['Person']['last_name'];
                $modelUserRegister->setPassword($post['UserRegister']['password']);
                $modelUserRegister->password_repeat = $modelUserRegister->password;

                $flag = $modelUserRegister->save();
            }

            if ($flag) {

                $modelUserPerson = new UserPerson();
                $modelUserPerson->user_id = $modelUserRegister->id;
                $modelUserPerson->person_id = $modelPerson->id;

                $flag = $modelUserPerson->save();
            }

            if ($flag) {

                $transaction->commit();

                Yii::$app->session->setFlash('message', [
                    'type' => 'success',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-tick-in-circle',
                    'message' => 'Anda telah terdaftar di Asikmakan',
                    'title' => 'Berhasil Mendaftar',
                ]);

                return $this->redirect(['site/register']);
            } else {

                $transaction->rollBack();

                $modelUserRegister->password = '';

                Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal mendaftar di Asikmakan',
                    'title' => 'Gagal Mendaftar',
                ]);
            }
        }

        return $this->render('register', [
            'modelUserRegister' => $modelUserRegister,
            'modelPerson' => $modelPerson,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $post = Yii::$app->request->post();
        $model = new LoginForm();

        if (!empty($post['loginButton']) && $model->load($post) && $model->login()) {

            return $this->goBack(['page/default']);
        } else {

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
