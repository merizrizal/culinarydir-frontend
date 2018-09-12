<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\UserRegister;
use core\models\Person;
use core\models\UserPerson;
use core\models\User;
use core\models\UserSocialMedia;
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
            'auth' => [
                'class' => \yii\authclient\AuthAction::class,
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $get = Yii::$app->request->get();

        $modelUserRegister = new UserRegister();
        $modelPerson = new Person();
        $modelUserSocialMedia = new UserSocialMedia();

        if (!empty($get['socmed'])) {

            $modelUserRegister->email = $get['email'];
            $modelPerson->first_name = $get['first_name'];
            $modelPerson->last_name = $get['last_name'];

            if ($get['socmed'] === 'Facebook') {

                $modelUserSocialMedia->facebook_id = $get['socmedId'];

            } else if ($get['socmed'] === 'Google') {

                $modelUserSocialMedia->google_id = $get['socmedId'];

            }
        }

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

            if ($flag && (!empty($post['UserSocialMedia']['facebook_id']) || !empty($post['UserSocialMedia']['google_id']))) {

                $modelUserSocialMedia->user_id = $modelUserRegister->id;

                if (!empty($post['UserSocialMedia']['google_id'])) {

                    $modelUserSocialMedia->google_id = $post['UserSocialMedia']['google_id'];

                } else if (!empty($post['UserSocialMedia']['facebook_id'])) {

                    $modelUserSocialMedia->facebook_id = $post['UserSocialMedia']['facebook_id'];

                }

                $flag = $modelUserSocialMedia->save();

                if ($flag) {
                    Yii::$app->mailer->compose([
                            'html' => 'registerConfirmation-html',
                            'text' => 'registerConfirmation-text'
                        ],
                        [
                            'email' => $post['UserRegister']['email'],
                            'full_name' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                            'socmed' => !empty($post['UserSocialMedia']['google_id']) ? 'Google' : 'Facebook',
                        ]
                    )
                    ->setFrom('asikmakan.bandung@gmail.com')
                    ->setTo($post['UserRegister']['email'])
                    ->setSubject('Welcome to ' . Yii::$app->name)
                    ->send();
                }
                print_r("SUCCESS");
                exit;
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
            'modelUserSocialMedia' => $modelUserSocialMedia,
            'socmed' => !empty($get['socmed']) ? $get['socmed'] : null,
            'socmedId' => !empty($get['socmedId']) ? $get['socmedId'] : null,
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

    public function onAuthSuccess($client)
    {
        $socmed = '';
        $socmedEmail = '';
        $first_name = '';
        $last_name = '';
        $loginFlag = false;
        $userAttributes = $client->getUserAttributes();

        if ($client->id === 'facebook') {

            $socmed = 'Facebook';
            $socmedEmail = $userAttributes['email'];
            $first_name = $userAttributes['first_name'];
            $last_name = $userAttributes['last_name'];

        } else if ($client->id === 'google') {

            $socmed = 'Google';
            $socmedEmail = $userAttributes['emails'][0]['value'];
            $first_name = $userAttributes['name']['givenName'];
            $last_name = $userAttributes['name']['familyName'];

        }

        $modelUser = User::find()
                ->joinWith(['userSocialMedia'])
                ->andWhere(['email' => $socmedEmail])
                ->one();

        if (empty($modelUser)) {

            return $this->redirect(['site/register',
                'socmed' => $socmed,
                'email' => $socmedEmail,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'socmedId' => $userAttributes['id'],
            ]);

        } else {

            $modelUserSocialMedia = !empty($modelUser->userSocialMedia) ? $modelUser->userSocialMedia : new UserSocialMedia();

            if ($socmed === 'Facebook') {

                if (empty($modelUserSocialMedia['facebook_id'])) {

                    $modelUserSocialMedia->user_id = $modelUser['id'];
                    $modelUserSocialMedia->facebook_id = $userAttributes['id'];
                    $loginFlag = $modelUserSocialMedia->save();

                } else {

                    if ($modelUserSocialMedia['facebook_id'] === $userAttributes['id']) {
                        $loginFlag = true;
                    }
                }

            } else if ($socmed === 'Google') {

                if (empty($modelUserSocialMedia['google_id'])) {

                    $modelUserSocialMedia->user_id = $modelUser['id'];
                    $modelUserSocialMedia->google_id = $userAttributes['id'];
                    $loginFlag = $modelUserSocialMedia->save();

                } else {

                    if ($modelUserSocialMedia['google_id'] === $userAttributes['id']) {
                        $loginFlag = true;
                    }
                }
            }

            if ($loginFlag) {

                $model = new LoginForm();
                $model->useSocmed = true;
                $model->login_id = $socmedEmail;

                if ($model->login()) {
                    return $this->goBack(['page/default']);
                }

            } else {

                Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal Login',
                    'title' => 'Gagal Login',
                ]);
            }
        }
    }
}
