<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\UserRegister;
use frontend\models\RequestResetPassword;
use frontend\models\ResetPassword;
use core\models\Person;
use core\models\UserPerson;
use core\models\User;
use core\models\UserSocialMedia;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;

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

        if ($modelUserRegister->load(($post = Yii::$app->request->post()))) {

            if (Yii::$app->request->isAjax) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($modelUserRegister);
            } else {

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

                    if (!empty($post['UserSocialMedia']['facebook_id']) || !empty($post['UserSocialMedia']['google_id'])) {

                        $socmedFLag = true;
                        $modelUserSocialMedia->user_id = $modelUserRegister->id;

                        if (!empty($post['UserSocialMedia']['google_id'])) {

                            $modelUserSocialMedia->google_id = $post['UserSocialMedia']['google_id'];
                        } else if (!empty($post['UserSocialMedia']['facebook_id'])) {

                            $modelUserSocialMedia->facebook_id = $post['UserSocialMedia']['facebook_id'];
                        }

                        if (($flag = $modelUserSocialMedia->save())) {

                            Yii::$app->mailer->compose(['html' => 'register_confirmation'], [
                                    'email' => $post['UserRegister']['email'],
                                    'full_name' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                                    'socmed' => !empty($post['UserSocialMedia']['google_id']) ? 'Google' : 'Facebook',
                                ]
                            )
                            ->setFrom(Yii::$app->params['supportEmail'])
                            ->setTo($post['UserRegister']['email'])
                            ->setSubject('Welcome to ' . Yii::$app->name)
                            ->send();
                        }
                    } else {

                        $socmedFLag = false;
                        $randomString = Yii::$app->security->generateRandomString();
                        $randomStringHalf = substr($randomString, 16);
                        $modelUserRegister->not_active = true;
                        $modelUserRegister->account_activation_token = substr($randomString, 0, 15) . $modelUserRegister->id . $randomStringHalf . '_' . time();

                        if (($flag = $modelUserRegister->save())) {

                            Yii::$app->mailer->compose(['html' => 'account_activation'], [
                                    'email' => $post['UserRegister']['email'],
                                    'full_name' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                                    'user' => $modelUserRegister
                                ]
                            )
                            ->setFrom(Yii::$app->params['supportEmail'])
                            ->setTo($post['UserRegister']['email'])
                            ->setSubject(Yii::$app->name . ' Account Activation')
                            ->send();
                        }
                    }
                }

                if ($flag) {

                    $transaction->commit();

                    if (!$socmedFLag) {
                        
                        return $this->render('message', [
                            'fullname' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                            'title' => Yii::t('app', 'You Have Registered To') . Yii::$app->name,
                            'messages' => 'Silakan aktivasi akun Anda dengan mengklik link yang sudah kami kirimkan ke email Anda di ' . $post['UserRegister']['email'] . '.',
                            'links' => '',
                        ]);
                    }

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
        }

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

        return $this->render('register', [
            'modelUserRegister' => $modelUserRegister,
            'modelPerson' => $modelPerson,
            'modelUserSocialMedia' => $modelUserSocialMedia,
            'socmed' => !empty($get['socmed']) ? $get['socmed'] : null,
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

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestResetPassword()
    {
        $model = new RequestResetPassword();
        if ($model->load(($post = Yii::$app->request->post())) && $model->validate()) {

            $modelUser = User::findByEmail($post);

            if ($model->sendEmail()) {

                $messageParams = [
                    'fullname' => $modelUser['full_name'],
                    'title' => Yii::t('app', 'Request Password Reset'),
                    'messages' => Yii::t('app', 'Check your email for further instructions'),
                    'links' => '',
                ];
            } else {

                $messageParams = [
                    'fullname' => $modelUser['full_name'],
                    'title' => Yii::t('app', 'Request Password Reset'),
                    'messages' => Yii::t('app', 'An error has occurred while requesting password reset'),
                    'links' => '',
                ];
            }

            return $this->render('message', $messageParams);
        }

        return $this->render('request_reset_password', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPassword($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {

            Yii::$app->session->setFlash('resetSuccess', 'Password baru Anda tersimpan.');

            return $this->redirect(['login']);
        }

        return $this->render('reset_password', [
            'model' => $model,
        ]);
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

                return $this->redirect(['login']);
            }
        }
    }

    public function actionActivateAccount($token) 
    {
        $modelUser = User::find()
            ->andWhere(['account_activation_token' => $token])
            ->andWhere(['not_active' => true])
            ->one();

        if (!empty($modelUser)) {

            $modelUser->not_active = false;
            $flag = $modelUser->save();

            if (!$flag) {

                Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal Aktivasi Akun Anda',
                    'title' => 'Gagal Aktivasi',
                ]);

                return $this->redirect(['register']);
            }
        }

        return $this->render('message', [
            'fullname' => $modelUser['full_name'],
            'title' => Yii::t('app', 'Your Account Has Been Activated'),
            'messages' => 'Silakan masuk dengan Email / Username Anda dengan mengklik link di bawah.',
            'links' => ['name' => Yii::t('app', 'Login To') . Yii::$app->name, 'url' => ['site/login']],
        ]);
    }
}