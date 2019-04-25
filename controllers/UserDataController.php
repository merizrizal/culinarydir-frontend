<?php
namespace frontend\controllers;

use core\models\BusinessPromo;
use core\models\TransactionSession;
use core\models\UserLove;
use core\models\UserPostMain;
use core\models\UserVisit;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

/**
 * User Data Controller
 */
class UserDataController extends base\BaseController
{

    /**
     *
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

    public function actionUserVisit($username)
    {
        if (!Yii::$app->request->isAjax) {

            $queryParams = Yii::$app->request->getQueryParams();

            $this->redirect(['user/user-profile',
                'user' => $username,
                'redirect' => 'visit',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {

            $this->layout = 'ajax';
        }

        $modelUserVisit = UserVisit::find()
            ->joinWith([
                'business',
                'business.businessImages' => function ($query) {

                    $query->andOnCondition(['business_image.type' => 'Profile'])
                        ->andOnCondition(['business_image.is_primary' => true]);
                },
                'business.businessLocation.village',
                'business.businessLocation.city',
                'user',
            ])
            ->andWhere(['user_visit.is_active' => true])
            ->andWhere(['user.username' => $username])
            ->distinct()
            ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelUserVisit,
        ]);

        $modelUserVisit = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelUserVisit) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        return $this->render('journey/user_visit', [
            'modelUserVisit' => $modelUserVisit,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionUserLove($username)
    {
        if (!Yii::$app->request->isAjax) {

            $queryParams = Yii::$app->request->getQueryParams();

            $this->redirect(['user/user-profile',
                'user' => $username,
                'redirect' => 'love',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {

            $this->layout = 'ajax';
        }

        $modelUserLove = UserLove::find()
            ->joinWith([
                'business',
                'business.businessImages' => function ($query) {

                    $query->andOnCondition(['business_image.type' => 'Profile'])
                        ->andOnCondition(['business_image.is_primary' => true]);
                },
                'business.businessLocation.village',
                'business.businessLocation.city',
                'user',
            ])
            ->andWhere(['user_love.is_active' => true])
            ->andWhere(['user.username' => $username])
            ->distinct()
            ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelUserLove,
        ]);

        $modelUserLove = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelUserLove) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('journey/user_love', [
            'modelUserLove' => $modelUserLove,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionUserPost($username)
    {
        if (!Yii::$app->request->isAjax) {

            $queryParams = Yii::$app->request->getQueryParams();

            $this->redirect(['user/user-profile',
                'user' => $username,
                'redirect' => 'review',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {

            $this->layout = 'ajax';
        }

        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
                'business.businessImages' => function ($query) {

                    $query->andOnCondition(['business_image.type' => 'Profile'])
                        ->andOnCondition(['business_image.is_primary' => true]);
                },
                'business.businessLocation.city',
                'user',
                'userPostMains child' => function ($query) {

                    $query->andOnCondition(['child.is_publish' => true])
                        ->andOnCondition(['child.type' => 'Photo']);
                },
                'userVotes',
                'userVotes.ratingComponent rating_component' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'userPostLoves' => function ($query) {

                    $query->andOnCondition(['user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null , 'user_post_love.is_active' => true]);
                },
                'userPostComments',
                'userPostComments.user user_comment',
            ])
            ->andWhere(['user_post_main.parent_id' => null])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->andWhere(['user_post_main.is_publish' => true])
            ->andWhere(['user.username' => $username])
            ->orderBy(['user_post_main.created_at' => SORT_DESC])
            ->distinct()
            ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelUserPostMain,
        ]);

        $modelUserPostMain = $provider->getModels();
        $pagination = $provider->getPagination();

        $pageSize = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelUserPostMain) ? $offset + 1 : 0;
        $endItem = min(($offset + $pageSize), $totalCount);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('journey/user_post', [
            'modelUserPostMain' => $modelUserPostMain,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionUserPostPhoto($username)
    {
        if (!Yii::$app->request->isAjax) {

            $queryParams = Yii::$app->request->getQueryParams();

            $this->redirect(['user/user-profile',
                'user' => $username,
                'redirect' => 'photo',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {

            $this->layout = 'ajax';
        }

        $modelUserPostMainPhoto = UserPostMain::find()
            ->joinWith([
                'user',
                'business',
            ])
            ->andWhere(['type' => 'Photo'])
            ->andWhere(['is_publish' => true])
            ->andWhere(['user.username' => $username])
            ->orderBy(['created_at' => SORT_DESC])
            ->distinct()
            ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelUserPostMainPhoto,
        ]);

        $modelUserPostMainPhoto = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelUserPostMainPhoto) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('user_post_photo', [
            'modelUserPostMainPhoto' => $modelUserPostMainPhoto,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionNewPromo()
    {
        $this->layout = 'ajax';

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $modelBusinessPromo = BusinessPromo::find()
            ->joinWith([
                'business.userLoves',
                'business.businessLocation.city'
            ])
            ->andWhere(['user_love.is_active' => true])
            ->andWhere(['user_love.user_id' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['not_active' => false])
            ->andWhere(['>=', 'date_end', Yii::$app->formatter->asDate(time())])
            ->orderBy('business_id')
            ->distinct()
            ->asArray();

        Yii::$app->formatter->timeZone = 'UTC';

        $provider = new ActiveDataProvider([
            'query' => $modelBusinessPromo,
        ]);

        $modelBusinessPromo = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelBusinessPromo) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        return $this->render('new_promo', [
            'modelBusinessPromo' => $modelBusinessPromo,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionOrderHistory()
    {
        if (!Yii::$app->request->isAjax) {

            $queryParams = Yii::$app->request->getQueryParams();

            $this->redirect(['user/user-profile',
                'user' => $queryParams['username'],
                'redirect' => 'order-history',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {

            $this->layout = 'ajax';
        }

        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'business',
                'business.businessImages' => function ($query) {

                    $query->andOnCondition(['business_image.type' => 'Profile'])
                        ->andOnCondition(['business_image.is_primary' => true]);
                },
                'business.businessLocation.city'
            ])
            ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->getIdentity()->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->distinct()
            ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelTransactionSession,
        ]);

        $modelTransactionSession = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelTransactionSession) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('order_history', [
           'modelTransactionSession' => $modelTransactionSession,
           'pagination' => $pagination,
           'startItem' => $startItem,
           'endItem' => $endItem,
           'totalCount' => $totalCount,
        ]);
    }
}