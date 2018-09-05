<?php
namespace frontend\controllers\base;

use Yii;
use yii\filters\AccessControl;

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

                            if (Yii::$app->session->get('user_data')['user_level']['is_super_admin']) {
                                return true;
                            }

                            $userAkses = Yii::$app->session->get('user_data')['user_level']['userAkses'];

                            foreach ($userAkses as $value) {

                                $module = '';

                                if (!empty($action->controller->module->id)){

                                    $module = $action->controller->module->id . '/';
                                }

                                if (
                                        $value['userAppModule']['nama_module'] === $action->controller->id
                                        && $value['userAppModule']['module_action'] === $action->id
                                        && $value['userAppModule']['sub_program'] === Yii::$app->params['subprogramLocal']
                                        && $value['is_active']
                                    ) {

                                    return true;
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

                                $userAppModule = Yii::$app->session->get('user_app_module');

                                foreach ($userAppModule as $value) {

                                    if (
                                        $value['nama_module'] === $action->controller->id
                                        && $value['module_action'] === $action->id
                                        && $value['sub_program'] === Yii::$app->params['subprogramLocal']
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

        if (empty(Yii::$app->session->get('user_app_module'))) {

            $userAppModule = \core\models\UserAppModule::find()
                            ->andWhere(['sub_program' => Yii::$app->params['subprogramLocal']])
                            ->asArray()->all();

            Yii::$app->session->set('user_app_module', $userAppModule);
        }

        return parent::beforeAction($action);
    }
}
