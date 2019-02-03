<?php

namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\UserRegister;
use frontend\models\RequestResetPassword;
use frontend\models\ResetPassword;
use core\models\Business;
use core\models\Person;
use core\models\UserPerson;
use core\models\User;
use core\models\UserSocialMedia;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\widgets\ActiveForm;
use core\models\UserLevel;

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
                    //'logout' => ['post'],
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
            
            return $this->goBack();
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
                $socmedFLag = false;
                
                $userLevel = UserLevel::find()
                    ->andWhere(['nama_level' => 'User'])
                    ->asArray()->one();
                
                $modelUserRegister->user_level_id = $userLevel['id'];
                $modelUserRegister->email = $post['UserRegister']['email'];
                $modelUserRegister->username = $post['UserRegister']['username'];
                $modelUserRegister->full_name = $post['Person']['first_name'] . ' ' . $post['Person']['last_name'];
                $modelUserRegister->setPassword($post['UserRegister']['password']);
                $modelUserRegister->password_repeat = $modelUserRegister->password;
                
                if (($flag = $modelUserRegister->save())) {
                    
                    $modelPerson->first_name = $post['Person']['first_name'];
                    $modelPerson->last_name = $post['Person']['last_name'];
                    $modelPerson->email = $post['UserRegister']['email'];
                    $modelPerson->phone = !empty($post['Person']['phone']) ? $post['Person']['phone'] : null;
                    $modelPerson->city_id = !empty($post['Person']['city_id']) ? $post['Person']['city_id'] : null;
                    
                    if (($flag = $modelPerson->save())) {
                        
                        $modelUserPerson = new UserPerson();
                        $modelUserPerson->user_id = $modelUserRegister->id;
                        $modelUserPerson->person_id = $modelPerson->id;
                        
                        if (($flag = $modelUserPerson->save())) {
                            
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
                                    ])
                                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                                    ->setTo($post['UserRegister']['email'])
                                    ->setSubject('Welcome to ' . Yii::$app->name)
                                    ->send();
                                }
                            } else {
                                
                                $randomString = Yii::$app->security->generateRandomString();
                                $randomStringHalf = substr($randomString, 16);
                                $modelUserRegister->not_active = true;
                                $modelUserRegister->account_activation_token = substr($randomString, 0, 15) . $modelUserRegister->id . $randomStringHalf . '_' . time();
                                
                                if (($flag = $modelUserRegister->save())) {
                                    
                                    Yii::$app->mailer->compose(['html' => 'account_activation'], [
                                        'email' => $post['UserRegister']['email'],
                                        'full_name' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                                        'userToken' => $modelUserRegister->account_activation_token
                                    ])
                                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                                    ->setTo($post['UserRegister']['email'])
                                    ->setSubject(Yii::$app->name . ' Account Activation')
                                    ->send();
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    $transaction->commit();

                    if (!$socmedFLag) {
                        
                        return $this->render('message', [
                            'fullname' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                            'title' => Yii::t('app', 'You Have Registered to {app}', ['app' => Yii::$app->name]),
                            'messages' => Yii::t('app', 'Please activate your account by clicking the link that we sent to your email at {email}.', ['email' => $post['UserRegister']['email']]),
                            'links' => '',
                        ]);
                    } else {
                        
                        return $this->render('message', [
                            'fullname' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                            'title' => Yii::t('app', 'You Have Successfully Registered to {app}', ['app' => Yii::$app->name]),
                            'messages' => Yii::t('app', 'Please login with your Email / Username by clicking the button below.'),
                            'links' => ['name' => Yii::t('app', 'Login to {app}', ['app' => Yii::$app->name]), 'url' => ['site/login']],
                        ]);
                    }
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
            
            return $this->goBack();
        }

        $post = Yii::$app->request->post();
        $model = new LoginForm();

        if (!empty($post['loginButton']) && $model->load($post) && $model->login()) {

            return $this->goBack();
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

            Yii::$app->session->setFlash('resetSuccess', Yii::t('app', 'Your new password has been saved'));

            return $this->redirect(['login']);
        }

        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    public function onAuthSuccess($client)
    {
        $loginFlag = false;
        $userAttributes = $client->getUserAttributes();

        if ($client->id === 'facebook') {

            $socmed = 'Facebook';
            $socmedEmail = $userAttributes['email'];
            $first_name = $userAttributes['first_name'];
            $last_name = $userAttributes['last_name'];
        } else if ($client->id === 'google') {

            $socmed = 'Google';
            $socmedEmail = $userAttributes['email'];
            $first_name = $userAttributes['given_name'];
            $last_name = $userAttributes['family_name'];
        }

        $modelUser = User::find()
            ->joinWith(['userSocialMedia'])
            ->andWhere(['email' => $socmedEmail])
            ->one();

        if (empty($modelUser)) {

            return $this->redirect(['register',
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

                    $modelUserSocialMedia->user_id = $modelUser->id;
                    $modelUserSocialMedia->facebook_id = $userAttributes['id'];
                    $loginFlag = $modelUserSocialMedia->save();
                } else {

                    $loginFlag = ($modelUserSocialMedia->facebook_id === $userAttributes['id']);
                }
            } else if ($socmed === 'Google') {

                if (empty($modelUserSocialMedia['google_id'])) {

                    $modelUserSocialMedia->user_id = $modelUser->id;
                    $modelUserSocialMedia->google_id = $userAttributes['id'];
                    $loginFlag = $modelUserSocialMedia->save();
                } else {

                    $loginFlag = ($modelUserSocialMedia->google_id === $userAttributes['id']);
                }
            }

            if ($loginFlag) {

                $model = new LoginForm();
                $model->useSocmed = true;
                $model->login_id = $socmedEmail;

                if ($model->login()) {
                    
                    return $this->goBack();
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

            if ($modelUser->save()) {
                
                return $this->render('message', [
                    'fullname' => $modelUser['full_name'],
                    'title' => Yii::t('app', 'Your Account Has Been Activated'),
                    'messages' => Yii::t('app', 'Please login with your Email / Username by clicking the button below.'),
                    'links' => ['name' => Yii::t('app', 'Login to {app}', ['app' => Yii::$app->name]), 'url' => ['site/login']],
                ]);
            } else {

                Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal Aktivasi Akun Anda',
                    'title' => 'Gagal Aktivasi',
                ]);

                return $this->redirect(['register']);
            }
        } else {
            
            return $this->redirect(['login']);
        }
    }
    
    public function actionSitemap() {
        
        $outputData = '';
        
        $modelBusiness = Business::find()
            ->joinWith([
                'businessLocation',
                'businessLocation.city',
                'userPostMains' => function($query) {
                    
                    $query->andOnCondition(['user_post_main.parent_id' => null])
                        ->andOnCondition(['user_post_main.type' => 'Review'])
                        ->andOnCondition(['user_post_main.is_publish' => true]);
                }
            ])
            ->andWhere('strpos("business"."unique_name", \'-\') > 0')
            ->asArray()->all();

        foreach ($modelBusiness as $dataBusiness) {
            
            $outputData .= '
                <url>
                    <loc>' . Yii::$app->urlManager->createAbsoluteUrl([
                        'page/detail', 
                        'city' => Inflector::slug($dataBusiness['businessLocation']['city']['name']),
                        'uniqueName' => $dataBusiness['unique_name']
                    ]) . '</loc>
                    <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.8</priority>
                </url>
                <url>
                    <loc>' . Yii::$app->urlManager->createAbsoluteUrl([
                        'page/menu',
                        'uniqueName' => $dataBusiness['unique_name']
                    ]) . '</loc>
                    <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.64</priority>
                </url>
            ';
            
            foreach ($dataBusiness['userPostMains'] as $dataUserPostMain) {
                
                $outputData .= '
                    <url>
                        <loc>' . Yii::$app->urlManager->createAbsoluteUrl([
                            'page/review',
                            'id' => $dataUserPostMain['id'],
                            'uniqueName' => $dataBusiness['unique_name']
                        ]) . '</loc>
                        <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                        <changefreq>monthly</changefreq>
                        <priority>0.64</priority>
                    </url>
                ';
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        
        return '<?xml version="1.0" encoding="UTF-8"?>
            <urlset 
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
                    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

                <url>
                    <loc>' . Yii::$app->urlManager->createAbsoluteUrl('') . '</loc>
                    <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>1</priority>
                </url>
                <url>
                    <loc>' . Yii::$app->urlManager->createAbsoluteUrl(['site/login']) . '</loc>
                    <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.8</priority>
                </url>
                <url>
                    <loc>' . Yii::$app->urlManager->createAbsoluteUrl(['site/register']) . '</loc>
                    <lastmod>' . Yii::$app->formatter->asDate(time()) . '</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.8</priority>
                </url>' . 
                $outputData . '
            </urlset>
        ';
    }
}