<?php
namespace frontend\controllers;

use Yii;
use core\models\ProductCategory;
use core\models\Business;
use core\models\BusinessDetail;
use core\models\BusinessDetailVote;
use core\models\UserPostMain;
use core\models\UserPostComment;
use core\models\RatingComponent;
use core\models\BusinessPromo;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
                    ]
                ]
            ]);
    }

    public function actionProductCategory()
    {
        $this->layout = 'ajax';

        $modelProductCategory = ProductCategory::find()
            ->orderBy(['name' => SORT_ASC])
            ->andFilterWhere(['ilike', 'name', Yii::$app->request->post('keyword')])
            ->asArray()->all();

        $productCategory = [];

        foreach ($modelProductCategory as $dataProductCategory) {

            if ($dataProductCategory['is_parent']) {

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
        if (!Yii::$app->request->isAjax) {
            
            $this->redirect(str_replace('/data-kuliner/di/', '/kuliner/di/', Yii::$app->request->getUrl()));
        } else {
            
            $this->layout = 'ajax';
        }
        
        return $this->getResult('result_list');
    }

    public function actionResultMap()
    {
        if (!Yii::$app->request->isAjax) {
            
            $this->redirect(str_replace('/map/data-kuliner/di/', '/map/kuliner/di/', Yii::$app->request->getUrl()));
        } else {
            
            $this->layout = 'ajax';
        }
        
        return $this->getResult('result_map');
    }

    public function actionPostReview()
    {
        if (!Yii::$app->request->isAjax) {
            
            $queryParams = Yii::$app->request->getQueryParams();
            
            $this->redirect(['page/detail', 
                'id' => $queryParams['business_id'],
                'redirect' => 'review', 
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {
            
            $this->layout = 'ajax';
        }

        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'user',
                'userPostMains child' => function ($query) {
                    
                    $query->andOnCondition(['child.is_publish' => true]);
                },
                'userVotes',
                'userVotes.ratingComponent rating_component' => function ($query) {
                    
                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'userPostLoves' => function ($query) {
                    
                    $query->andOnCondition([
                        'user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_post_love.is_active' => true
                    ]);
                },
                'userPostComments',
                'userPostComments.user user_comment',
            ])
            ->andWhere(['user_post_main.parent_id' => null])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->andWhere(['user_post_main.business_id' => Yii::$app->request->get('business_id')])
            ->andWhere(['user_post_main.is_publish' => true])
            ->andFilterWhere(['<>', 'user_post_main.user_id', !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
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
        if (!Yii::$app->request->isAjax) {
            
            $queryParams = Yii::$app->request->getQueryParams();
            
            $this->redirect(['page/detail',
                'id' => $queryParams['business_id'],
                'redirect' => 'photo',
                'page' => !empty($queryParams['page']) ? $queryParams['page'] : 1,
                'per-page' => !empty($queryParams['per-page']) ? $queryParams['per-page'] : '',
            ]);
        } else {
            
            $this->layout = 'ajax';
        }

        $modelUserPostMain = UserPostMain::find()
            ->andWhere(['type' => 'Photo'])
            ->andWhere(['business_id' => Yii::$app->request->get('business_id')])
            ->andWhere(['is_publish' => true])
            ->orderBy(['created_at' => SORT_DESC])
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
            ->orderBy(['user_post_comment.id' => SORT_ASC])
            ->distinct()
            ->asArray()->all();

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('post_comment', [
            'userPostId' => Yii::$app->request->post('user_post_main_id'),
            'modelUserPostComment' => $modelUserPostComment,
        ]);
    }

    public function actionBusinessRating()
    {
        $this->layout = 'ajax';

        $modelBusinessDetail = BusinessDetail::find()
            ->andWhere(['business_detail.business_id' => Yii::$app->request->post('business_id')])
            ->asArray()->one();

        $modelBusinessDetailVote = BusinessDetailVote::find()
            ->joinWith([
                'ratingComponent' => function($query) {
                    
                    $query->andOnCondition(['is_active' => true]);
                }
            ])
            ->andWhere(['business_detail_vote.business_id' => Yii::$app->request->post('business_id')])
            ->asArray()->all();

        $modelRatingComponent = RatingComponent::find()
            ->where(['is_active' => true])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()->all();

        return $this->render('business_rating', [
            'modelBusinessDetail' => $modelBusinessDetail,
            'modelBusinessDetailVote' => $modelBusinessDetailVote,
            'modelRatingComponent' => $modelRatingComponent,
        ]);
    }
    
    public function actionRecentPost()
    {
        if (!Yii::$app->request->isAjax) {
            
            $this->redirect(['page/index']);
        } else {
            
            $this->layout = 'ajax';
        }
        
        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
                'business.businessLocation',
                'business.businessLocation.city',
                'user',
                'userPostMains child' => function ($query) {
                
                    $query->andOnCondition(['child.is_publish' => true]);
                },
                'userPostLoves' => function ($query) {
                
                    $query->andOnCondition([
                        'user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_post_love.is_active' => true
                    ]);
                },
                'userVotes',
                'userPostComments',
            ])
            ->andWhere(['user_post_main.parent_id' => null])
            ->andWhere(['user_post_main.is_publish' => true])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->orderBy(['user_post_main.created_at' => SORT_DESC])
            ->distinct()
            ->asArray();
            
        $dataProviderUserPostMain = new ActiveDataProvider([
            'query' => $modelUserPostMain,
            'pagination' => [
                'route' => 'data/recent-post',
                'pageSize' => 9,
            ]
        ]);
        
        return $this->render('recent_post', [
            'dataProviderUserPostMain' => $dataProviderUserPostMain,
        ]);
    }

    private function getResult($fileRender)
    {
        $get = Yii::$app->request->get();
        $paramsView = [];
        
        if ($get['tp'] == 1 || $get['tp'] == 3) {
            
            $modelBusiness = Business::find()
                ->joinWith([
                    'businessCategories' => function($query) {
                    
                        $query->andOnCondition(['business_category.is_active' => true]);
                    },
                    'businessCategories.category',
                    'businessImages' => function($query) {
                    
                        $query->andOnCondition(['type' => 'Profile']);
                    },
                    'businessLocation',
                    'businessLocation.city',
                    'businessProductCategories' => function($query) {
                    
                        $query->andOnCondition(['business_product_category.is_active' => true]);
                    },
                    'businessProductCategories.productCategory' => function ($query) {
                    
                        $query->andOnCondition(['product_category.is_active' => true]);
                    },
                    'businessDetail',
                    'userLoves' => function($query) {
                    
                        $query->andOnCondition([
                            'user_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                            'user_love.is_active' => true
                        ]);
                    },
                    'membershipType.membershipTypeProductServices.productService',
                ])
                ->andFilterWhere(['business_location.city_id' => $get['cty']])
                ->andFilterWhere(['lower(city.name)' => str_replace('-', ' ', $get['city'])])
                ->andFilterWhere(['or', ['ilike', 'business.name', $get['nm']], ['ilike', 'product_category.name', $get['nm']], ['ilike', 'business_location.address', $get['nm']]]);
            
            if ($get['tp'] == 1) {
                
                $modelBusiness = $modelBusiness->andFilterWhere(['business_product_category.product_category_id' => $get['pct']])
                    ->andFilterWhere(['business_category.category_id' => $get['ctg']]);
            
                if (!empty($get['pmn']) || !empty($get['pmx'])) {
                    
                    if ($get['pmn'] == 0 && $get['pmx'] != 0) {
                        
                        $modelBusiness = $modelBusiness->andFilterWhere(['<=', 'business_detail.price_max', $get['pmx']]);
                    } else if ($get['pmn'] != 0 && $get['pmx'] == 0) {
                        
                        $modelBusiness = $modelBusiness->andFilterWhere(['>=', 'business_detail.price_min', $get['pmn']]);
                    } else if ($get['pmn'] != 0 && $get['pmx'] != 0) {
                        
                        $modelBusiness = $modelBusiness->andFilterWhere([
                            'or',
                            ['between', 'business_detail.price_min', $get['pmn'], $get['pmx']],
                            ['between', 'business_detail.price_max', $get['pmn'], $get['pmx']]
                        ]);
                    }
                }
                
                if (!empty($get['fct'])) {

                    $facilityCondition = '';
                    
                    foreach ($get['fct'] as $facilityId) {
                        
                        $facilityCondition .= 'business_facility.facility_id = ' . $facilityId . ' OR ';
                    }
                        
                    $facilityCondition = '
                        (SELECT COUNT(business_facility.facility_id)
                            FROM business_facility
                            WHERE business_facility.business_id = business.id
                                AND business_facility.is_active = TRUE
                                AND (' . trim($facilityCondition, 'OR ') . '))';
                    
                    $modelBusiness = $modelBusiness->andFilterWhere([$facilityCondition => count($get['fct'])]);
                }
                
                if (!empty($get['cmp'])) {
                    
                    $coordinate = explode(', ', $get['cmp']);
                    $latitude = $coordinate[0];
                    $longitude = $coordinate[1];
                    $radius = $get['rmp'];
                    
                    $modelBusiness = $modelBusiness->andWhere('(acos( sin( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * sin( radians( ' . $latitude . ' ) ) + cos( radians( split_part( "business_location"."coordinate" , \',\', 1)::double precision ) ) * cos( radians( ' . $latitude . ' )) * cos( radians( split_part( "business_location"."coordinate" , \',\', 2)::double precision ) - radians( ' . $longitude . ' )) ) * 6356 * 1000) <= ' . $radius . '');
                }
            } else {
                
                $modelBusiness = $modelBusiness->andFilterWhere(['product_service.code_name' => 'order-online']);
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
            
            $paramsView['modelBusiness'] = $modelBusiness;
        } else if ($get['tp'] == 2) {
            
            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
            
            $fileRender .= '_special';
            
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
                ->andFilterWhere(['business_location.city_id' => $get['cty']])
                ->andFilterWhere(['or', ['ilike', 'business.name', $get['nm']], ['ilike', 'product_category.name', $get['nm']], ['ilike', 'business_location.address', $get['nm']]])
                ->andFilterWhere(['business_product_category.product_category_id' => $get['pct']])
                ->andFilterWhere(['business_category.category_id' => $get['ctg']])
                ->andFilterWhere(['>=', 'date_end', Yii::$app->formatter->asDate(time())])
                ->andFilterWhere(['business_promo.not_active' => false]);
                
            Yii::$app->formatter->timeZone = 'UTC';
                
            if (!empty($get['cmp'])) {
                
                $coordinate = explode(', ', $get['cmp']);
                $latitude = $coordinate[0];
                $longitude = $coordinate[1];
                $radius = $get['rmp'];
                
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
            
            $paramsView['modelBusinessPromo'] = $modelBusinessPromo;
        }
        
        $paramsView = ArrayHelper::merge($paramsView, [
            'pagination' => $pagination,
            'startItem' => $startItem,
            'endItem' => $endItem,
            'totalCount' => $totalCount
        ]);

        return $this->render($fileRender, $paramsView);
    }
}