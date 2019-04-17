<?php

namespace frontend\controllers;

use Yii;
use core\models\Business;
use core\models\BusinessPromo;
use core\models\ProductCategory;
use core\models\RatingComponent;
use core\models\TransactionSession;
use core\models\UserReport;
use core\models\UserPostMain;
use frontend\models\Post;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use core\models\City;
use core\models\Promo;

/**
 * Page Controller
 */
class PageController extends base\BaseHistoryUrlController
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

                    ]
                ]
            ]);
    }

    public function actionIndex()
    {
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
                'userPostComments'
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
        
        $city = City::find()->andWhere(['name' => 'Bandung'])->asArray()->one();
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $modelPromo = Promo::find()
            ->andWhere(['not_active' => false])
            ->andWhere(['OR', ['>=', 'date_end', Yii::$app->formatter->asDate(time())], ['date_end' => null]])
            ->asArray()->all();
        
        Yii::$app->formatter->timeZone = 'UTC';
        
        $keyword = [];
        $keyword['searchType'] = Yii::t('app', 'favorite');
        $keyword['city'] = $city['id'];
        $keyword['name'] = null;
        $keyword['product']['id'] = null;
        $keyword['product']['name'] = null;
        $keyword['category'] = null;
        $keyword['map']['coordinate'] = null;
        $keyword['map']['radius'] = null;
        $keyword['facility'] = null;
        $keyword['price']['min'] = null;
        $keyword['price']['max'] = null;

        return $this->render('index', [
            'dataProviderUserPostMain' => $dataProviderUserPostMain,
            'keyword' => $keyword,
            'modelPromo' => $modelPromo
        ]);
    }

    public function actionResultList()
    {
        return $this->getResult('result_list');
    }

    public function actionResultMap()
    {
        return $this->getResult('result_map');
    }

    public function actionDetail($city, $uniqueName)
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $modelBusiness = Business::find()
            ->joinWith([
                'businessCategories' => function ($query) {

                    $query->andOnCondition(['business_category.is_active' => true]);
                },
                'businessCategories.category',
                'businessFacilities' => function ($query) {

                    $query->andOnCondition(['business_facility.is_active' => true]);
                },
                'businessFacilities.facility',
                'businessImages',
                'businessLocation',
                'businessLocation.city',
                'businessLocation.district',
                'businessLocation.village',
                'businessProducts' => function ($query) {

                    $query->andOnCondition(['business_product.not_active' => false]);
                },
                'businessProductCategories' => function ($query) {

                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'businessProductCategories.productCategory' => function ($query) {
                    
                    $query->andOnCondition(['<>', 'product_category.type', 'Menu']);
                },
                'businessDetail',
                'businessHours' => function ($query) {

                    $query->andOnCondition(['business_hour.is_open' => true])
                        ->orderBy(['business_hour.day' => SORT_ASC]);
                },
                'businessHours.businessHourAdditionals',
                'businessDetailVotes',
                'businessDetailVotes.ratingComponent rating_component' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'businessPromos' => function ($query) {

                    $query->andOnCondition(['>=', 'business_promo.date_end', Yii::$app->formatter->asDate(time())])
                        ->andOnCondition(['business_promo.not_active' => false]);
                },
                'membershipType' => function ($query) {
                
                    $query->andOnCondition(['ilike', 'membership_type.name', 'Order Online']);
                },
                'userLoves' => function ($query) {

                    $query->andOnCondition([
                        'user_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_love.is_active' => true
                    ]);
                },
                'userVisits' => function ($query) {

                    $query->andOnCondition([
                        'user_visit.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_visit.is_active' => true
                    ]);
                }
            ])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->andWhere(['lower(city.name)' => str_replace('-', ' ', $city)])
            ->asArray()->one();
        
        if (empty($modelBusiness)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'user',
                'userPostMains child' => function ($query) {

                    $query->andOnCondition(['child.is_publish' => true])
                        ->orderBy(['child.created_at' => SORT_ASC]);
                },
                'userVotes' => function ($query) {

                    $query->orderBy(['rating_component_id' => SORT_ASC]);
                },
                'userVotes.ratingComponent' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'userPostLoves' => function ($query) {

                    $query->andOnCondition([
                        'user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_post_love.is_active' => true
                    ]);
                },
                'userPostComments',
                'userPostComments.user user_comment'
            ])
            ->andWhere(['user_post_main.parent_id' => null])
            ->andWhere(['user_post_main.business_id' => $modelBusiness['id']])
            ->andWhere(['user_post_main.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->andWhere(['user_post_main.is_publish' => true])
            ->asArray()->one();       

        $modelRatingComponent = RatingComponent::find()
            ->where(['is_active' => true])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()->all();
        
        $modelTransactionSession = TransactionSession::find()
            ->joinWith(['business'])
            ->andWhere(['transaction_session.user_ordered' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['transaction_session.is_closed' => false])
            ->asArray()->one();

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
                'ratingComponentValue' => $ratingComponentValue
            ];
        }
        
        $modelPost = new Post();
        
        $modelPostPhoto = new Post();
        $modelPostPhoto->setScenario('postPhoto');

        if (!empty($modelUserPostMain)) {

            $modelPost->text = $modelUserPostMain['text'];
        }
        
        $dataBusinessImage = [];
        
        foreach ($modelBusiness['businessImages'] as $businessImage) {
            
            $dataBusinessImage[$businessImage['category']][] = $businessImage;
        }

        Yii::$app->formatter->timeZone = 'UTC';
        
        return $this->render('detail', [
            'modelBusiness' => $modelBusiness,
            'dataBusinessImage' => $dataBusinessImage,
            'modelUserPostMain' => $modelUserPostMain,
            'dataUserVoteReview' => $dataUserVoteReview,
            'modelPost' => $modelPost,
            'modelPostPhoto' => $modelPostPhoto,
            'modelRatingComponent' => $modelRatingComponent,
            'modelUserReport' => $modelUserReport,
            'modelTransactionSession' => $modelTransactionSession,
            'queryParams' => Yii::$app->request->getQueryParams(),
        ]);
    }

    public function actionDetailPromo($id, $uniqueName)
    {
        $modelBusinessPromo = BusinessPromo::find()
            ->joinWith([
                'business',
                'business.businessLocation',
                'business.businessLocation.city',
            ])
            ->andWhere(['business_promo.id' => $id])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->asArray()->one();
        
        if (empty($modelBusinessPromo)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('detail_promo', [
            'modelBusinessPromo' => $modelBusinessPromo
        ]);
    }

    public function actionReview($id, $uniqueName)
    {
        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
                'business.businessImages',
                'business.businessLocation',
                'business.businessLocation.city',
                'business.businessLocation.district',
                'business.businessLocation.village',
                'business.businessProducts' => function ($query) {
                
                    $query->andOnCondition(['business_product.not_active' => false]);
                },
                'business.businessProductCategories' => function ($query) {
                
                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'business.businessProductCategories.productCategory' => function ($query) {
                
                    $query->andOnCondition(['product_category.is_active' => true]);
                },
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
                'userPostComments.user user_comment'
            ])
            ->andWhere(['user_post_main.id' => $id])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->andWhere(['user_post_main.is_publish' => true])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->asArray()->one();
                
        if (empty($modelUserPostMain)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
                
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
                'ratingComponentValue' => $ratingComponentValue
            ];
        }

        return $this->render('review', [
            'modelUserPostMain' => $modelUserPostMain,
            'dataUserVoteReview' => $dataUserVoteReview,
            'modelBusiness' => $modelUserPostMain['business'],
        ]);
    }

    public function actionPhoto($id, $uniqueName)
    {
        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
                'business.businessLocation',
                'business.businessLocation.city',
                'user',
                'userPostLoves' => function ($query) {

                    $query->andOnCondition([
                        'user_post_love.user_id' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null,
                        'user_post_love.is_active' => true
                    ]);
                },
                'userPostComments',
                'userPostComments.user user_comment'
            ])
            ->andWhere(['user_post_main.id' => $id])
            ->andWhere(['user_post_main.type' => 'Photo'])
            ->andWhere(['user_post_main.is_publish' => true])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->asArray()->one();
                
        if (empty($modelUserPostMain)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('photo', [
            'modelUserPostMain' => $modelUserPostMain
        ]);
    }
    
    public function actionMenu($uniqueName)
    {
        $modelBusiness = Business::find()
            ->joinWith([
                'businessLocation',
                'businessLocation.city',
                'businessProducts' => function ($query) {
                
                    $query->andOnCondition(['business_product.not_active' => false])
                        ->orderBy(['business_product.order' => SORT_ASC]);
                },
                'businessProducts.businessProductCategory' => function ($query) {
                
                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'businessProducts.businessProductCategory.productCategory' => function ($query) {
                    
                    $query->andOnCondition(['OR', ['product_category.type' => 'Menu'], ['product_category.type' => 'Specific-Menu']]);    
                },
                'membershipType' => function ($query) {
                
                    $query->andOnCondition(['ilike', 'membership_type.name', 'Order Online']);
                },
            ])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->asArray()->one();
            
        if (empty($modelBusiness)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
            
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'transactionItems' => function ($query) {
                
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'business'
            ])
            ->andWhere(['transaction_session.user_ordered' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['transaction_session.is_closed' => false])
            ->asArray()->one();
                
        $dataMenuCategorised = [];
                    
        foreach ($modelBusiness['businessProducts'] as $dataBusinessProduct) {
            
            if (!empty($dataBusinessProduct['businessProductCategory'])) {
                
                $key = $dataBusinessProduct['businessProductCategory']['productCategory']['id'] . '|' . $dataBusinessProduct['businessProductCategory']['productCategory']['name'];
                
                if (empty($dataMenuCategorised[$dataBusinessProduct['businessProductCategory']['order']][$key])) {
                    
                    $dataMenuCategorised[$dataBusinessProduct['businessProductCategory']['order']][$key] = [];
                }
                
                array_push($dataMenuCategorised[$dataBusinessProduct['businessProductCategory']['order']][$key], $dataBusinessProduct);
            } else {
                
                if (empty($dataMenuCategorised[999]['emptyCategory'])) {
                    
                    $dataMenuCategorised[999]['emptyCategory'] = [];
                }
                
                array_push($dataMenuCategorised[999]['emptyCategory'], $dataBusinessProduct);
            }
        }
        
        return $this->render('menu', [
            'modelBusiness' => $modelBusiness,
            'modelTransactionSession' => $modelTransactionSession,
            'dataMenuCategorised' => $dataMenuCategorised
        ]);
    }

    private function getResult($fileRender)
    {
        $get = Yii::$app->request->get();
        
        if (!empty($get['pct'])) {
            
            $modelProductCategory = ProductCategory::find()
                ->andFilterWhere(['id' => $get['pct']])
                ->asArray()->one();
        }
        
        $city = City::find()->andWhere(['name' => 'Bandung'])->asArray()->one();
        
        $keyword = [];
        $keyword['searchType'] = !empty($get['searchType']) ? $get['searchType'] : Yii::t('app', 'favorite');;
        $keyword['city'] = !empty($get['cty']) ? $get['cty'] : $city['id'];
        $keyword['name'] = !empty($get['nm']) ? $get['nm'] : null;
        $keyword['product']['id'] = !empty($get['pct']) ? $get['pct'] : null;
        $keyword['product']['name'] = !empty($modelProductCategory) ? $modelProductCategory['name'] : null;
        $keyword['category'] = !empty($get['ctg']) ? $get['ctg'] : null;
        $keyword['map']['coordinate'] = !empty($get['cmp']) ? $get['cmp'] : null;
        $keyword['map']['radius'] = !empty($get['rmp']) ? $get['rmp'] : null;
        $keyword['facility'] = !empty($get['fct']) ? $get['fct'] : null;
        $keyword['price']['min'] = ($keyword['searchType'] == Yii::t('app', 'favorite') || $keyword['searchType'] == Yii::t('app', 'online-order')) && $get['pmn'] !== null && $get['pmn'] !== '' ? $get['pmn'] : null;
        $keyword['price']['max'] = ($keyword['searchType'] == Yii::t('app', 'favorite') || $keyword['searchType'] == Yii::t('app', 'online-order')) && $get['pmx'] !== null && $get['pmx'] !== '' ? $get['pmx'] : null;

        
        Yii::$app->session->set('keyword', $get);

        return $this->render($fileRender, [
            'keyword' => $keyword,
            'params' => $get
        ]);
    }
}
