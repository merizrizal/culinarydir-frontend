<?php
namespace frontend\controllers\base;

use yii\filters\AccessControl;
use common\models\LoginForm;

class BaseController extends \yii\web\Controller
{
    public function getAccess()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {

                            $userData = \Yii::$app->session->get('user_data');

                            if (!empty($userData['user_level']) && !empty($userData['user_akses'])) {

                                foreach ($userData['user_level'] as $dataLevel) {

                                    if ($dataLevel['is_super_admin']) {

                                        return true;
                                    }
                                }

                                foreach ($userData['user_akses'] as $dataAkses) {

                                    if (
                                            $dataAkses['userAppModule']['nama_module'] === $action->controller->id
                                            && $dataAkses['userAppModule']['module_action'] === $action->id
                                            && $dataAkses['userAppModule']['sub_program'] === \Yii::$app->params['subprogramLocal']
                                            && $dataAkses['is_active']
                                    ) {

                                        return true;
                                    }
                                }
                            }

                            if ($action->controller->id === 'site') {

                                return true;
                            }

                            return false;
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'matchCallback' => function ($rule, $action) {

                            if ($action->controller->id === 'site') {

                                return true;
                            } else {

                                $userAppModule = \Yii::$app->session->get('user_app_module');

                                foreach ($userAppModule as $value) {

                                    if (
                                            $value['nama_module'] === $action->controller->id
                                            && $value['module_action'] === $action->id
                                            && $value['sub_program'] === \Yii::$app->params['subprogramLocal']
                                            && $value['guest_can_access']
                                    ) {

                                        return true;
                                    }
                                }

                                return false;
                            }
                        }
                    ],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->getView()->params['assetCommon'] = \common\assets\AppAsset::register($this->getView());

        if (!empty(\Yii::$app->request->get('token'))) {

            if (\Yii::$app->user->isGuest) {

                $modelLoginForm = new LoginForm([
                    'useToken' => true,
                    'token' => \Yii::$app->request->get('token')
                ]);

                $modelLoginForm->login();
            }
        }

        if (empty(\Yii::$app->session->get('user_app_module'))) {

            $userAppModule = \core\models\UserAppModule::find()
                            ->andWhere(['sub_program' => \Yii::$app->params['subprogramLocal']])
                            ->asArray()->all();

            \Yii::$app->session->set('user_app_module', $userAppModule);
        }

        return parent::beforeAction($action);
    }
}
