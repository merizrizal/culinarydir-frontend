<?php
namespace frontend\controllers;

use Yii;
use backend\models\UserVisit;
use backend\models\UserLove;
use backend\models\UserPostMain;
use frontend\models\Post;
use backend\models\BusinessPromo;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * User Data Controller
 */
class UserDataController extends base\BaseController
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

    public function actionGetUserVisit()
    {
        $this->layout = 'ajax';

        $modelUserVisit = UserVisit::find()
                ->joinWith([
                    'business',
                    'business.businessImages' => function($query) {
                        $query->andOnCondition([
                            'business_image.type' => 'Profile',
                            'business_image.is_primary' => true
                        ]);
                    },
                    'business.businessLocation.village',
                    'business.businessLocation.city',
                    'user',
                ])
                ->where(['user_visit.is_active' => true]);

        if (!empty(Yii::$app->request->get('username'))) {

            $modelUserVisit->andFilterWhere(['user.username' => Yii::$app->request->get('username')]);
        } else if (!empty(Yii::$app->user->getIdentity()->id)) {

            $modelUserVisit->andFilterWhere(['user_visit.user_id' => Yii::$app->user->getIdentity()->id]);
        }

        $modelUserVisit->distinct()
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

        return $this->render('journey/get_user_visit', [
            'modelUserVisit' => $modelUserVisit,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionGetUserLove()
    {
        $this->layout = 'ajax';

        $modelUserLove = UserLove::find()
                ->joinWith([
                    'business',
                    'business.businessCategories.category',
                    'business.businessFacilities.facility',
                    'business.businessImages' => function($query) {
                        $query->andOnCondition([
                            'business_image.type' => 'Profile',
                            'business_image.is_primary' => true
                        ]);
                    },
                    'business.businessLocation.village',
                    'business.businessLocation.city',
                    'business.businessProductCategories.productCategory',
                    'user',
                ])
                ->where(['user_love.is_active' => true]);

        if (!empty(Yii::$app->request->get('username'))) {

            $modelUserLove->andFilterWhere(['user.username' => Yii::$app->request->get('username')]);
        } else if (!empty(Yii::$app->user->getIdentity()->id)) {

            $modelUserLove->andFilterWhere(['user_love.user_id' => Yii::$app->user->getIdentity()->id]);
        }

        $modelUserLove->distinct()
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

        return $this->render('journey/get_user_love', [
            'modelUserLove' => $modelUserLove,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionGetUserPost()
    {
        $this->layout = 'ajax';

        $modelUserPostMain = UserPostMain::find()
                ->joinWith([
                    'business',
                    'business.businessImages' => function($query) {
                        $query->andOnCondition([
                            'business_image.type' => 'Profile',
                            'business_image.is_primary' => true]);
                    },
                    'userPostMains child' => function($query) {
                        $query->andOnCondition(['child.is_publish' => true]);
                    },
                    'user',
                    'userVotes',
                    'userVotes.ratingComponent',
                    'userPostComments',
                    'userPostComments.user user_comment',
                    'userPostLoves' => function($query) {
                        $query->andOnCondition(['user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null , 'user_post_love.is_active' => true]);
                    },
                ])
                ->andWhere(['user_post_main.type' => 'Review'])
                ->andWhere(['user_post_main.is_publish' => true]);

        if (!empty(Yii::$app->request->get('username'))) {

            $modelUserPostMain->andFilterWhere(['user.username' => Yii::$app->request->get('username')]);
        } else if (!empty(Yii::$app->user->getIdentity()->id)) {

            $modelUserPostMain->andFilterWhere(['user_post_main.user_id' => Yii::$app->user->getIdentity()->id]);
        }

        $modelUserPostMain->orderBy(['id' => SORT_DESC])
                ->distinct()
                ->asArray();

        $provider = new ActiveDataProvider([
            'query' => $modelUserPostMain,
        ]);

        $modelPost = new Post();

        $modelUserPostMain = $provider->getModels();
        $pagination = $provider->getPagination();

        $perpage = $pagination->pageSize;
        $totalCount = $pagination->totalCount;
        $offset = $pagination->offset;

        $startItem = !empty($modelUserPostMain) ? $offset + 1 : 0;
        $endItem = min(($offset + $perpage), $totalCount);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('journey/get_user_post', [
            'modelUserPostMain' => $modelUserPostMain,
            'modelPost' => $modelPost,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionGetUserPostPhoto()
    {
        $this->layout = 'ajax';

        $modelUserPostMainPhoto = UserPostMain::find()
                ->joinWith('user')
                ->andWhere(['type' => 'Photo'])
                ->andWhere(['is_publish' => true]);

        if (!empty(Yii::$app->request->get('username'))) {

            $modelUserPostMainPhoto->andFilterWhere(['user.username' => Yii::$app->request->get('username')]);
        } else if (!empty(Yii::$app->user->getIdentity()->id)) {

            $modelUserPostMainPhoto->andFilterWhere(['user_id' => Yii::$app->user->getIdentity()->id]);
        }

        $modelUserPostMainPhoto->orderBy(['id' => SORT_DESC])
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

        return $this->render('get_user_post_photo', [
            'modelUserPostMainPhoto' => $modelUserPostMainPhoto,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionGetNewPromo()
    {
        $this->layout = 'ajax';

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $modelBusinessPromo = BusinessPromo::find()
                ->joinWith([
                    'business.userLoves'
                ])
                ->andWhere([
                    'user_love.is_active' => true,
                    'user_love.user_id' => Yii::$app->user->getIdentity()->id,
                    'not_active' => false
                ])
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

        return $this->render('get_new_promo', [
            'modelBusinessPromo' => $modelBusinessPromo,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }
}