<?php
namespace frontend\controllers;

use Yii;
use backend\models\Business;
use backend\models\BusinessPromo;
use backend\models\ProductCategory;
use backend\models\RatingComponent;
use backend\models\UserReport;
use backend\models\UserPostMain;
use frontend\models\Post;
use yii\filters\VerbFilter;

/**
 * Page Controller
 */
class PageController extends base\BaseHistoryUrlController
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

    public function actionDefault()
    {
        return $this->redirect(Yii::$app->session->get('user_data')['user_level']['default_action']);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionResultList()
    {
        return $this->getResult('result_list');
    }

    public function actionResultMap()
    {
        return $this->getResult('result_map');
    }

    public function actionDetail($id)
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $modelBusiness = Business::find()
                ->joinWith([
                    'businessCategories' => function($query) {
                        $query->andOnCondition(['business_category.is_active' => true]);
                    },
                    'businessCategories.category',
                    'businessFacilities' => function($query) {
                        $query->andOnCondition(['business_facility.is_active' => true]);
                    },
                    'businessFacilities.facility',
                    'businessImages',
                    'businessLocation',
                    'businessProductCategories' => function($query) {
                        $query->andOnCondition(['business_product_category.is_active' => true]);
                    },
                    'businessProductCategories.productCategory',
                    'businessImages',
                    'businessDetail',
                    'businessHours' => function($query) {
                        $query->andOnCondition(['business_hour.is_open' => true]);
                    },
                    'businessDetailVotes',
                    'businessDetailVotes.ratingComponent rating_component' => function($query) {
                        $query->andOnCondition(['rating_component.is_active' => true]);
                    },
                    'businessPromos' => function($query) {
                        $query->andOnCondition(['>=', 'date_end', Yii::$app->formatter->asDate(time())])
                            ->andOnCondition(['business_promo.not_active' => false]);
                    },
                    'userLoves' => function($query) {
                        $query->andOnCondition([
                            'user_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                            'user_love.is_active' => true
                        ]);
                    },
                    'userVisits' => function($query) {
                        $query->andOnCondition([
                            'user_visit.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                            'user_visit.is_active' => true
                        ]);
                    },
                    'businessMenus' => function($query) {
                        $query->andOnCondition(['business_menu.not_active' => false]);
                    },
                ])
                ->andWhere(['business.id' => $id])
                ->asArray()->one();

        Yii::$app->formatter->timeZone = 'UTC';

        $modelUserPostMain = UserPostMain::find()
                ->joinWith([
                    'user',
                    'userPostMains child' => function($query) {
                        $query->andOnCondition(['child.is_publish' => true])
                                ->orderBy(['child.created_at' => SORT_ASC]);
                    },
                    'userVotes' => function($query) {
                        $query->orderBy(['rating_component_id' => SORT_ASC]);
                    },
                    'userVotes.ratingComponent rating_component' => function($query) {
                        $query->andOnCondition(['rating_component.is_active' => true]);
                    },
                    'userPostLoves' => function($query) {
                        $query->andOnCondition(['user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null , 'user_post_love.is_active' => true]);
                    },
                    'userPostComments',
                    'userPostComments.user user_comment',
                ])
                ->andWhere(['user_post_main.parent_id' => null])
                ->andWhere(['user_post_main.business_id' => $id])
                ->andWhere(['user_post_main.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
                ->andWhere(['user_post_main.type' => 'Review'])
                ->andWhere(['user_post_main.is_publish' => true])
                ->asArray()->one();

        $modelPost = new Post();

        $modelPostPhoto = new Post();
        $modelPostPhoto->setScenario('postPhoto');

        $modelRatingComponent = RatingComponent::find()
                ->where(['is_active' => true])
                ->orderBy(['order' => SORT_ASC])
                ->asArray()->all();

        $modelUserReport = new UserReport();

        $dataUserVoteReview = [];

        if (!empty($modelUserPostMain['userVotes'])) {

            $ratingComponentValue = [];
            $totalVoteValue = 0;

            foreach ($modelUserPostMain['userVotes'] as $dataUserVote) {

                if (!empty($dataUserVote['ratingComponent'])) {

                    $totalVoteValue += $dataUserVote['vote_value'];

                    $ratingComponentValue[$dataUserVote['rating_component_id']] = $dataUserVote['vote_value'];
                }
            }

            $overallValue = !empty($totalVoteValue) && !empty($ratingComponentValue) ? ($totalVoteValue / count($ratingComponentValue)) : 0;

            $dataUserVoteReview = [
                'overallValue' => $overallValue,
                'ratingComponentValue' => $ratingComponentValue,
            ];
        }

        if (!empty($modelUserPostMain)) {

            $modelPost->text = $modelUserPostMain['text'];
        }

        return $this->render('detail', [
            'modelBusiness' => $modelBusiness,
            'modelUserPostMain' => $modelUserPostMain,
            'dataUserVoteReview' => $dataUserVoteReview,
            'modelPost' => $modelPost,
            'modelPostPhoto' => $modelPostPhoto,
            'modelRatingComponent' => $modelRatingComponent,
            'modelUserReport' => $modelUserReport,
        ]);
    }

    public function actionDetailPromo($id)
    {
        $modelBusinessPromo = BusinessPromo::find()
                ->andWhere(['id' => $id])
                ->distinct()
                ->asArray()->one();

        return $this->render('detail_promo',[
            'modelBusinessPromo' => $modelBusinessPromo,
        ]);
    }

    private function getResult($fileRender)
    {

        $get = Yii::$app->request->get();

        $keyword = [];

        if (!empty($get['special'])) {

            $keyword['special'] = $get['special'];
        }

        if (!empty($get['city_id'])) {

            $keyword['city'] = $get['city_id'];
        }

        if (!empty($get['name'])) {

            $keyword['name'] = $get['name'];
        }

        if (!empty($get['product_category'])) {

            $modelProductCategory = ProductCategory::find()
                    ->where(['id' => $get['product_category']])
                    ->asArray()->one();

            $keyword['product']['id'] = $get['product_category'];
            $keyword['product']['name'] = $modelProductCategory['name'];
        }

        if (!empty($get['category_id'])) {

            $keyword['category'] = $get['category_id'];
        }

        if (!empty($get['price_min'])) {

            $keyword['price_min'] = $get['price_min'];
        }

        if (!empty($get['price_max'])) {

            $keyword['price_max'] = $get['price_max'];
        }

        if (!empty($get['coordinate_map'])) {

            $keyword['coordinate'] = $get['coordinate_map'];
        }

        if (!empty($get['radius_map'])) {

            $keyword['radius'] = $get['radius_map'];
        }

        if (!empty($get['facility_id'])) {

            $keyword['facility'] = $get['facility_id'];
        }

        Yii::$app->session->setFlash('keyword', $keyword);

        return $this->render($fileRender, [
            'keyword' => $keyword,
        ]);
    }
}
