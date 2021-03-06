<?php
namespace frontend\controllers;

use Yii;
use backend\models\ProductCategory;
use backend\models\Business;
use backend\models\BusinessDetail;
use backend\models\BusinessDetailVote;
use backend\models\UserPostMain;
use backend\models\UserPostComment;
use backend\models\RatingComponent;
use backend\models\BusinessPromo;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * Data Controller
 */
class DataController extends base\BaseController
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
                        'product-category' => ['POST'],
                        'post-comment' => ['POST'],
                        'business-rating' => ['POST'],
                    ],
                ],
            ]);
    }

    public function actionProductCategory()
    {
        $this->layout = 'ajax';

        $modelProductCategory = ProductCategory::find()
                ->joinWith(['parent parent'])
                ->orderBy(['name' => SORT_ASC])
                ->andFilterWhere(['ilike', 'product_category.name', Yii::$app->request->post('keyword')])
                ->asArray()->all();

        $productCategory = [];

        foreach ($modelProductCategory as $dataProductCategory)
        {

            if (empty($dataProductCategory['parent']['id'])) {

                $productCategory['parent'][] = $dataProductCategory;
            } else {

                $productCategory['child'][] = $dataProductCategory;
            }
        }

        return $this->render('product_category', [
            'productCategory' => $productCategory,
        ]);
    }

    public function actionResultList()
    {
        return $this->getResult('list');
    }

    public function actionResultMap()
    {
        return $this->getResult('map');
    }

    public function actionPostReview()
    {
        $this->layout = 'ajax';

        $modelUserPostMain = UserPostMain::find()
                ->joinWith([
                    'user',
                    'userPostMains child' => function($query) {
                        $query->andOnCondition(['child.is_publish' => true]);
                    },
                    'userVotes',
                    'userVotes.ratingComponent rating_component' => function($query) {
                        $query->andOnCondition(['rating_component.is_active' => true]);
                    },
                    'userPostLoves' => function($query) {
                        $query->andOnCondition(['user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null, 'user_post_love.is_active' => true]);
                    },
                    'userPostComments',
                    'userPostComments.user user_comment',
                ])
                ->andWhere(['user_post_main.parent_id' => null])
                ->andWhere(['user_post_main.type' => 'Review'])
                ->andWhere(['user_post_main.business_id' => Yii::$app->request->get('business_id')])
                ->andWhere(['user_post_main.is_publish' => true])
                ->andFilterWhere(['<>', 'user_post_main.user_id', !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
                ->orderBy(['user_post_main.id' => SORT_DESC])
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

        return $this->render('post_review', [
            'modelUserPostMain' => $modelUserPostMain,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionPostPhoto()
    {
        $this->layout = 'ajax';

        $modelUserPostMain = UserPostMain::find()
                ->andWhere(['type' => 'Photo'])
                ->andWhere(['business_id' => Yii::$app->request->get('business_id')])
                ->andWhere(['is_publish' => true])
                ->orderBy(['id' => SORT_DESC])
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

        return $this->render('post_photo', [
            'modelUserPostMain' => $modelUserPostMain,
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionPostComment()
    {
        $this->layout = 'ajax';

        $modelUserPostComment = UserPostComment::find()
                ->joinWith([
                    'user',
                    'userPostMain',
                ])
                ->andWhere(['user_post_comment.user_post_main_id' => Yii::$app->request->post('user_post_main_id')])
                ->orderBy(['id' => SORT_DESC])
                ->distinct()
                ->asArray()->all();

        $userPostComment = [];

        foreach ($modelUserPostComment as $dataUserPostComment) {

            $userPostComment[$dataUserPostComment['user_post_main_id']][] = $dataUserPostComment;
        }

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('post_comment', [
            'userPostComment' => $userPostComment,
        ]);
    }

    public function actionBusinessRating()
    {
        $this->layout = 'ajax';

        $modelBusinessDetail = BusinessDetail::find()
                ->andWhere(['business_detail.business_id' => Yii::$app->request->post('business_id')])
                ->asArray()->one();

        $modelBusinessDetailVote = BusinessDetailVote::find()
                ->joinWith(['ratingComponent'])
                ->andWhere(['business_detail_vote.business_id' => Yii::$app->request->post('business_id')])
                ->asArray()->all();

        $modelRatingComponent = RatingComponent::find()
                ->andWhere(['rating_component.is_active' => true])
                ->asArray()->all();

        return $this->render('business_rating', [
            'modelBusinessDetail' => $modelBusinessDetail,
            'modelBusinessDetailVote' => $modelBusinessDetailVote,
            'modelRatingComponent' => $modelRatingComponent,
        ]);
    }

    private function getResult($typePage)
    {
        $this->layout = 'ajax';

        $get = Yii::$app->request->get();

        if (!$get['special']) {

            $modelBusiness = Business::find()
                    ->joinWith([
                        'membershipType',
                        'businessCategories' => function($query) {
                            $query->andOnCondition(['business_category.is_active' => true]);
                        },
                        'businessCategories.category',
                        'businessFacilities' => function($query) {
                            $query->andOnCondition(['business_facility.is_active' => true]);
                        },
                        'businessFacilities.facility',
                        'businessImages' => function($query) {
                            $query->andOnCondition(['type' => 'Profile']);
                        },
                        'businessLocation',
                        'businessProductCategories' => function($query) {
                            $query->andOnCondition(['business_product_category.is_active' => true]);
                        },
                        'businessProductCategories.productCategory',
                        'businessDetail',
                        'userLoves' => function($query) {
                            $query->andOnCondition([
                                'user_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                                'user_love.is_active' => true
                            ]);
                        },
                    ])
                    ->andFilterWhere(['business_location.city_id' => $get['city_id']])
                    ->andFilterWhere(['or', ['ilike', 'business.name', $get['name']], ['ilike', 'product_category.name', $get['name']]])
                    ->andFilterWhere(['business_product_category.product_category_id' => $get['product_category']])
                    ->andFilterWhere(['business_category.category_id' => $get['category_id']]);

            if (!empty($get['price_min']) || !empty($get['price_max'])) {

                if ($get['price_min'] == 0 && $get['price_max'] != 0) {

                    $modelBusiness = $modelBusiness->andFilterWhere(['<=', 'business_detail.price_max', $get['price_max']]);
                }

                if ($get['price_min'] != 0 && $get['price_max'] == 0) {

                    $modelBusiness = $modelBusiness->andFilterWhere(['>=', 'business_detail.price_min', $get['price_min']]);
                }

                if ($get['price_min'] != 0 && $get['price_max'] != 0) {

                    $modelBusiness = $modelBusiness->andFilterWhere(['>=', 'business_detail.price_min', $get['price_min']])
                            ->andFilterWhere(['<=', 'business_detail.price_max', $get['price_max']]);
                }
            }

            if (!empty($get['facility_id'])) {
                $modelBusiness = $modelBusiness->andFilterWhere(['business_facility.facility_id' => $get['facility_id']]);
            }

            if (!empty($get['coordinate_map'])) {

                $coordinate = explode(', ', $get['coordinate_map']);
                $latitude = $coordinate[0];
                $longitude = $coordinate[1];
                $radius = $get['radius_map'];

                $modelBusiness = $modelBusiness->andWhere('(acos( sin( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * sin( radians( ' . $latitude . ' ) ) + cos( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * cos( radians( ' . $latitude . ' )) * cos( radians( split_part( "business_location"."coordinate" , \',\', 2)::double precision ) - radians( ' . $longitude . ' )) ) * 6356 * 1000) <= ' . $radius . '');
            }

            $modelBusiness = $modelBusiness->orderBy(['business.id' => SORT_DESC])
                    ->distinct()
                    ->asArray();

            $provider = new ActiveDataProvider([
                'query' => $modelBusiness,
            ]);

            $modelBusiness = $provider->getModels();
            $pagination = $provider->getPagination();

            $pageSize = $pagination->pageSize;
            $totalCount = $pagination->totalCount;
            $offset = $pagination->offset;

            $startItem = !empty($modelBusiness) ? $offset + 1 : 0;
            $endItem = min(($offset + $pageSize), $totalCount);

            if ($typePage == 'list') {

                $fileRender = 'result_list';
            } else if ($typePage == 'map') {

                $fileRender = 'result_map';
            }
        } else if ($get['special']) {

            Yii::$app->formatter->timeZone = 'Asia/Jakarta';

            $modelBusinessPromo = BusinessPromo::find()
                    ->joinWith([
                        'business',
                        'business.businessCategories' => function($query) {
                            $query->andOnCondition(['business_category.is_active' => true]);
                        },
                        'business.businessCategories.category',
                        'business.businessLocation',
                        'business.businessProductCategories' => function($query) {
                            $query->andOnCondition(['business_product_category.is_active' => true]);
                        },
                        'business.businessProductCategories.productCategory',
                    ])
                    ->andFilterWhere(['business_location.city_id' => $get['city_id']])
                    ->andFilterWhere(['or', ['ilike', 'business.name', $get['name']], ['ilike', 'product_category.name', $get['name']]])
                    ->andFilterWhere(['business_product_category.product_category_id' => $get['product_category']])
                    ->andFilterWhere(['business_category.category_id' => $get['category_id']])
                    ->andFilterWhere(['>=', 'date_end', Yii::$app->formatter->asDate(time())])
                    ->andFilterWhere(['business_promo.not_active' => false]);

            Yii::$app->formatter->timeZone = 'UTC';

            if (!empty($get['coordinate_map'])) {

                $coordinate = explode(', ', $get['coordinate_map']);
                $latitude = $coordinate[0];
                $longitude = $coordinate[1];
                $radius = $get['radius_map'];

                $modelBusinessPromo = $modelBusinessPromo->andWhere('(acos( sin( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * sin( radians( ' . $latitude . ' ) ) + cos( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * cos( radians( ' . $latitude . ' )) * cos( radians( split_part( "business_location"."coordinate" , \',\', 2)::double precision ) - radians( ' . $longitude . ' )) ) * 6356 * 1000) <= ' . $radius . '');
            }

            $modelBusinessPromo = $modelBusinessPromo->orderBy(['business_promo.id' => SORT_DESC])
                    ->distinct()
                    ->asArray();

            $provider = new ActiveDataProvider([
                'query' => $modelBusinessPromo,
            ]);

            $modelBusinessPromo = $provider->getModels();
            $pagination = $provider->getPagination();

            $pageSize = $pagination->pageSize;
            $totalCount = $pagination->totalCount;
            $offset = $pagination->offset;

            $startItem = !empty($modelBusinessPromo) ? $offset + 1 : 0;
            $endItem = min(($offset + $pageSize), $totalCount);

            if ($typePage == 'list') {

                $fileRender = 'result_list_special';
            } else if ($typePage == 'map') {

                $fileRender = 'result_map_special';
            }
        }

        return $this->render($fileRender, [
            'modelBusiness' => !empty($modelBusiness) ? $modelBusiness : [],
            'modelBusinessPromo' => !empty($modelBusinessPromo) ? $modelBusinessPromo : [],
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount,
        ]);
    }
}
