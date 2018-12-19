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
                'user',
                'userPostMains child' => function ($query) {

                    $query->andOnCondition(['child.is_publish' => true]);
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
                'route' => 'data/recent-post'
            ]
        ]);

        return $this->render('index', [
            'dataProviderUserPostMain' => $dataProviderUserPostMain
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

    public function actionDetail($id)
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
                'businessProducts' => function ($query) {

                    $query->andOnCondition(['business_product.not_active' => false]);
                },
                'businessProductCategories',
                'businessProductCategories.productCategory' => function ($query) {
                
                    $query>andOnCondition(['business_product_category.is_active' => true])
                        ->andOnCondition(['product_category.is_active' => true]);
                },
                'businessDetail',
                'businessHours' => function ($query) {

                    $query->andOnCondition(['business_hour.is_open' => true])
                        ->orderBy(['business_hour.day' => SORT_ASC]);
                },
                'businessDetailVotes',
                'businessDetailVotes.ratingComponent rating_component' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'businessPromos' => function ($query) {

                    $query->andOnCondition(['>=', 'date_end', Yii::$app->formatter->asDate(time())])
                        ->andOnCondition(['business_promo.not_active' => false]);
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
            ->andWhere(['business.id' => $id])
            ->asArray()->one();

        Yii::$app->formatter->timeZone = 'UTC';
        
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
            ->andWhere(['user_post_main.business_id' => $id])
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

    public function actionDetailPromo($id)
    {
        $modelBusinessPromo = BusinessPromo::find()
            ->andWhere(['id' => $id])
            ->distinct()
            ->asArray()->one();
        
        if (empty($modelBusinessPromo)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('detail_promo', [
            'modelBusinessPromo' => $modelBusinessPromo
        ]);
    }

    public function actionReview($id)
    {
        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
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
            'dataUserVoteReview' => $dataUserVoteReview
        ]);
    }

    public function actionPhoto($id)
    {
        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'business',
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
            ->asArray()->one();
                
        if (empty($modelUserPostMain)) {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('photo', [
            'modelUserPostMain' => $modelUserPostMain
        ]);
    }
    
    public function actionMenu($id)
    {
        $modelBusiness = Business::find()
            ->joinWith([
                'businessProducts' => function ($query) {
                
                    $query->andOnCondition(['business_product.not_active' => false]);
                },
            ])
            ->andWhere(['business.id' => $id])
            ->asArray()->one();
        
        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'transactionItems' => function($query) {
                
                    $query->orderBy(['transaction_item.id' => SORT_ASC]);
                },
                'business'
            ])
            ->andWhere(['transaction_session.user_ordered' => !empty(Yii::$app->user->getIdentity()->id) ? Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['transaction_session.is_closed' => false])
            ->asArray()->one();
        
        return $this->render('menu', [
            'modelBusiness' => $modelBusiness,
            'modelTransactionSession' => $modelTransactionSession
        ]);
    }

    private function getResult($fileRender)
    {
        $filter = Yii::$app->request->get();

        $keyword = [];

        if (!empty($filter['special'])) {

            $keyword['special'] = $filter['special'];
        }

        if (!empty($filter['city_id'])) {

            $keyword['city'] = $filter['city_id'];
        }

        if (!empty($filter['name'])) {

            $keyword['name'] = $filter['name'];
        }

        if (!empty($filter['product_category'])) {

            $modelProductCategory = ProductCategory::find()
                ->where(['id' => $filter['product_category']])
                ->asArray()->one();

            $keyword['product']['id'] = $filter['product_category'];
            $keyword['product']['name'] = $modelProductCategory['name'];
        }

        if (!empty($filter['category_id'])) {

            $keyword['category'] = $filter['category_id'];
        }

        if (!empty($filter['price_min'])) {

            $keyword['price_min'] = $filter['price_min'];
        }

        if (!empty($filter['price_max'])) {

            $keyword['price_max'] = $filter['price_max'];
        }

        if (!empty($filter['coordinate_map'])) {

            $keyword['coordinate'] = $filter['coordinate_map'];
        }

        if (!empty($filter['radius_map'])) {

            $keyword['radius'] = $filter['radius_map'];
        }

        if (!empty($filter['facility_id'])) {

            $keyword['facility'] = $filter['facility_id'];
        }

        Yii::$app->session->setFlash('keyword', $keyword);

        return $this->render($fileRender, [
            'keyword' => $keyword,
            'filter' => $filter
        ]);
    }
}
