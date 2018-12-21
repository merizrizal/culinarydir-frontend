<?php
namespace frontend\controllers;

use Yii;
use core\models\TransactionSession;
use core\models\UserPerson;
use core\models\User;
use frontend\models\ChangePassword;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\widgets\ActiveForm;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;

/**
 * User Controller
 */
class UserController extends base\BaseHistoryUrlController
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

    public function actionIndex()
    {
        $modelUser = User::find()
            ->joinWith(['userPerson.person'])
            ->andWhere(['user.id' => Yii::$app->user->getIdentity()->id])
            ->asArray()->one();
    
        return $this->render('index', [
            'modelUser' => $modelUser,
            'queryParams' => Yii::$app->request->getQueryParams(),
        ]);
    }

    public function actionUserProfile()
    {
        if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->username == Yii::$app->request->get('user')) {

            return $this->redirect(ArrayHelper::merge(['user/index'], Yii::$app->request->getQueryParams()));
        } else {
            
            $modelUser = User::find()
                ->joinWith(['userPerson.person'])
                ->andWhere(['username' => Yii::$app->request->get('user')])
                ->asArray()->one();
            
            if (empty($modelUser)) {
                
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            return $this->render('user_profile', [
                'modelUser' => $modelUser,
                'queryParams' => Yii::$app->request->getQueryParams(),
            ]);
        }
    }

    public function actionUpdateProfile()
    {
        $modelUserPerson = UserPerson::find()
            ->joinWith([
                'user',
                'person',
            ])
            ->andWhere(['user_person.user_id' => Yii::$app->user->getIdentity()->id])
            ->one();

        $modelUser = $modelUserPerson->user;
        $modelPerson = $modelUserPerson->person;        
        
        if (Yii::$app->request->isAjax && $modelUser->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelUser);
        }

        if (!empty(($post = Yii::$app->request->post())) && $modelPerson->load($post) && $modelUser->load($post)) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;            

            if (($flag = $modelPerson->save())) { 

                if (!($modelUser->image = Tools::uploadFile('/img/user/', $modelUser, 'image', 'username', $modelUser->username))) {

                    $modelUser->image = $modelUser->oldAttributes['image'];
                }

                $modelUser->full_name = $modelPerson->first_name . ' ' . $modelPerson->last_name;

                $flag = $modelUser->save();
            }

            if ($flag) {

                $transaction->commit();

                Yii::$app->session->setFlash('message', [
                    'type' => 'success',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-tick-in-circle',
                    'message' => 'Anda berhasil mengubah profile Anda di Asikmakan',
                    'title' => 'Berhasil Update Profile',
                ]);

                return $this->redirect(['user/update-profile']);
            } else {

                $transaction->rollBack();

                Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal mengubah profile Anda di Asikmakan',
                    'title' => 'Gagal Update Profile',
                ]);
            }
        }

        return $this->render('update_profile', [
            'modelUserPerson' => $modelUserPerson,
            'modelUser' => $modelUser,
            'modelPerson' => $modelPerson,            
        ]);
    }

    public function actionChangePassword()
    {
        try {
            $modelChangePassword = new ChangePassword(Yii::$app->user->getIdentity()->id);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($modelChangePassword->load(Yii::$app->request->post()) && $modelChangePassword->validate() && $modelChangePassword->changePassword()) {

            Yii::$app->session->setFlash('message', [
                'message' => 'Anda berhasil mengubah password baru di Asikmakan',
            ]);
        }

        return $this->render('change_password', [
            'modelChangePassword' => $modelChangePassword,
        ]);
    }
    
    public function actionDetailOrderHistory($id)
    {
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'business',
                'business.businessImages' => function($query) {
                
                    $query->andOnCondition(['business_image.is_primary' => true]);
                },
                'business.businessLocation',
                'transactionItems' => function($query) {
                
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'transactionItems.businessProduct'
            ])
            ->andWhere(['transaction_session.id' => $id])
            ->asArray()->one();
                
        if (empty($modelTransactionSession)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        return $this->render('detail_order_history', [
            'modelTransactionSession' => $modelTransactionSession
        ]);
    }
}
