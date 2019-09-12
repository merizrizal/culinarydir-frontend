<?php

namespace frontend\controllers;

use core\models\Business;
use core\models\BusinessHour;
use core\models\BusinessProductCategory;
use core\models\BusinessPromo;
use core\models\City;
use core\models\ProductCategory;
use core\models\Promo;
use core\models\RatingComponent;
use core\models\TransactionSession;
use core\models\UserLove;
use core\models\UserPostMain;
use core\models\UserReport;
use core\models\UserVisit;
use frontend\models\Post;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
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
                'business.businessLocation',
                'business.businessLocation.city',
                'user',
                'userPostMains child' => function ($query) {

                    $query->andOnCondition(['child.is_publish' => true])
                        ->andOnCondition(['child.type' => 'Photo']);
                },
                'userPostLoves' => function ($query) {

                    $query->andOnCondition(['user_post_love.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
                        ->andOnCondition(['user_post_love.is_active' => true]);
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

        \Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $modelPromo = Promo::find()
            ->andWhere(['not_active' => false])
            ->andWhere(['OR', ['>=', 'date_end', \Yii::$app->formatter->asDate(time())], ['date_end' => null]])
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()->all();

        $city = City::find()->andWhere(['name' => 'Bandung'])->asArray()->one();

        \Yii::$app->formatter->timeZone = 'UTC';

        $keyword = [];
        $keyword['searchType'] = \Yii::t('app', 'favorite');
        $keyword['city'] = $city['id'];
        $keyword['cityName'] = $city['name'];
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
        \Yii::$app->formatter->timeZone = 'Asia/Jakarta';

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
                'businessDetail',
                'businessDetailVotes',
                'businessDetailVotes.ratingComponent rating_component' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'businessPromos' => function ($query) {

                    $query->andOnCondition(['>=', 'business_promo.date_end', \Yii::$app->formatter->asDate(time())])
                        ->andOnCondition(['business_promo.not_active' => false]);
                },
                'membershipType' => function ($query) {

                    $query->andOnCondition(['membership_type.is_active' => true]);
                },
                'membershipType.membershipTypeProductServices' => function ($query) {

                    $query->andOnCondition(['membership_type_product_service.not_active' => false]);
                },
                'membershipType.membershipTypeProductServices.productService' => function ($query) {

                    $query->andOnCondition(['product_service.code_name' => 'order-online'])
                        ->andOnCondition(['product_service.not_active' => false]);
                }
            ])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->andWhere(['lower(city.name)' => str_replace('-', ' ', $city)])
            ->cache(60)
            ->asArray()->one();

        $modelBusiness['userLoves'] = UserLove::find()
            ->andWhere(['user_love.business_id' => $modelBusiness['id']])
            ->andWhere(['user_love.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['user_love.is_active' => true])
            ->asArray()->all();

        $modelBusiness['userVisits'] = UserVisit::find()
            ->andWhere(['user_visit.business_id' => $modelBusiness['id']])
            ->andWhere(['user_visit.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['user_visit.is_active' => true])
            ->asArray()->all();

        $modelBusiness['businessProductCategories'] = BusinessProductCategory::find()
            ->joinWith([
                'productCategory' => function ($query) {

                    $query->andOnCondition(['<>', 'product_category.type', 'Menu']);
                }
            ])
            ->andWhere(['business_product_category.business_id' => $modelBusiness['id']])
            ->andWhere(['business_product_category.is_active' => true])
            ->cache(60)
            ->asArray()->all();

        $modelBusiness['businessHours'] = BusinessHour::find()
            ->joinWith(['businessHourAdditionals'])
            ->andWhere(['business_hour.business_id' => $modelBusiness['id']])
            ->andWhere(['business_hour.is_open' => true])
            ->orderBy(['business_hour.day' => SORT_ASC])
            ->cache(60)
            ->asArray()->all();

        $isOrderOnline = false;

        if (empty($modelBusiness)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        } else {

            if (!empty($modelBusiness['membershipType']['membershipTypeProductServices'])) {

                foreach ($modelBusiness['membershipType']['membershipTypeProductServices'] as $membershipTypeProductService) {

                    if (($isOrderOnline = !empty($membershipTypeProductService['productService']))) {

                        break;
                    }
                }
            }
        }

        $modelUserPostMain = UserPostMain::find()
            ->joinWith([
                'user',
                'userPostMains child' => function ($query) {

                    $query->andOnCondition(['child.is_publish' => true])
                        ->andOnCondition(['child.type' => 'Photo'])
                        ->orderBy(['child.created_at' => SORT_ASC]);
                },
                'userVotes' => function ($query) {

                    $query->orderBy(['rating_component_id' => SORT_ASC]);
                },
                'userVotes.ratingComponent' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'userPostLoves' => function ($query) {

                    $query->andOnCondition(['user_post_love.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
                        ->andOnCondition(['user_post_love.is_active' => true]);
                },
                'userPostComments',
                'userPostComments.user user_comment'
            ])
            ->andWhere(['user_post_main.parent_id' => null])
            ->andWhere(['user_post_main.business_id' => $modelBusiness['id']])
            ->andWhere(['user_post_main.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['user_post_main.type' => 'Review'])
            ->andWhere(['user_post_main.is_publish' => true])
            ->cache(60)
            ->asArray()->one();

        $modelRatingComponent = RatingComponent::find()
            ->where(['is_active' => true])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()->all();

        $modelTransactionSession = TransactionSession::find()
            ->joinWith(['business'])
            ->andWhere(['transaction_session.user_ordered' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['transaction_session.status' => 'Open'])
            ->cache(60)
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

        $businessWhatsApp = !empty($modelBusiness['phone3']) ? 'https://api.whatsapp.com/send?phone=62' . substr(str_replace('-', '', $modelBusiness['phone3']), 1) : null;

        \Yii::$app->formatter->timeZone = 'UTC';

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
            'queryParams' => \Yii::$app->request->getQueryParams(),
            'isOrderOnline' => $isOrderOnline,
            'businessWhatsApp' => $businessWhatsApp
        ]);
    }

    public function actionDetailBusinessPromo($id, $uniqueName)
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

        return $this->render('detail_business_promo', [
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

                    $query->andOnCondition(['child.is_publish' => true])
                        ->andOnCondition(['child.type' => 'Photo']);
                },
                'userVotes',
                'userVotes.ratingComponent rating_component' => function ($query) {

                    $query->andOnCondition(['rating_component.is_active' => true]);
                },
                'userPostLoves' => function ($query) {

                    $query->andOnCondition(['user_post_love.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
                        ->andOnCondition(['user_post_love.is_active' => true]);
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

                    $query->andOnCondition(['user_post_love.user_id' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
                        ->andOnCondition(['user_post_love.is_active' => true]);
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

                    $query->andOnCondition(['membership_type.is_active' => true]);
                },
                'membershipType.membershipTypeProductServices' => function ($query) {

                    $query->andOnCondition(['membership_type_product_service.not_active' => false]);
                },
                'membershipType.membershipTypeProductServices.productService' => function ($query) {

                    $query->andOnCondition(['product_service.code_name' => 'order-online'])
                        ->andOnCondition(['product_service.not_active' => false]);
                },
            ])
            ->andWhere(['business.unique_name' => $uniqueName])
            ->asArray()->one();

        $isOrderOnline = false;

        if (empty($modelBusiness)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        } else {

            if (!empty($modelBusiness['membershipType']['membershipTypeProductServices'])) {

                foreach ($modelBusiness['membershipType']['membershipTypeProductServices'] as $membershipTypeProductService) {

                    if (($isOrderOnline = !empty($membershipTypeProductService['productService']))) {

                        break;
                    }
                }
            }
        }

        $modelTransactionSession = TransactionSession::find()
            ->joinWith([
                'transactionItems' => function ($query) {

                    $query->orderBy(['transaction_item.created_at' => SORT_ASC]);
                },
                'business'
            ])
            ->andWhere(['transaction_session.user_ordered' => !empty(\Yii::$app->user->getIdentity()->id) ? \Yii::$app->user->getIdentity()->id : null])
            ->andWhere(['transaction_session.status' => 'Open'])
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
            'dataMenuCategorised' => $dataMenuCategorised,
            'isOrderOnline' => $isOrderOnline
        ]);
    }

    public function actionDetailPromo($id)
    {
        $modelPromo = Promo::find()
            ->joinWith(['userPromoItems'])
            ->andWhere(['id' => $id])
            ->asArray()->one();

        if (empty($modelPromo)) {

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $countUserClaimed = count($modelPromo['userPromoItems']);
        $claimInfo = \Yii::t('app', '{userClaimed} user have claimed this promo', ['userClaimed' => $countUserClaimed]);

        if ($countUserClaimed == 0) {

            $claimInfo = \Yii::t('app', 'No user has claimed this promo yet');
        }

        if (!empty(\Yii::$app->user->getIdentity()->id)) {

            foreach ($modelPromo['userPromoItems'] as $dataUserPromoItem) {

                if ($dataUserPromoItem['user_id'] == \Yii::$app->user->getIdentity()->id) {

                    $claimInfo = \Yii::t('app', 'You and {userClaimed} other user have claimed this promo', ['userClaimed' => $countUserClaimed - 1]);

                    if (($countUserClaimed - 1) == 0) {

                        $claimInfo = \Yii::t('app', 'You have claimed this promo');
                    }

                    break;
                }
            }
        }

        return $this->render('detail_promo', [
            'modelPromo' => $modelPromo,
            'claimInfo' => $claimInfo,
            'countUserClaimed' => $countUserClaimed
        ]);
    }

    private function getResult($fileRender)
    {
        $get = \Yii::$app->request->get();

        if (!empty($get['pct'])) {

            $modelProductCategory = ProductCategory::find()
                ->andFilterWhere(['id' => $get['pct']])
                ->asArray()->one();
        }

        $city = City::find()->andWhere(['name' => 'Bandung'])->asArray()->one();

        $keyword = [];
        $keyword['searchType'] = !empty($get['searchType']) ? $get['searchType'] : \Yii::t('app', 'favorite');
        $keyword['cityName'] = $city['name'];
        $keyword['city'] = !empty($get['cty']) ? $get['cty'] : $city['id'];
        $keyword['name'] = !empty($get['nm']) ? $get['nm'] : null;
        $keyword['product']['id'] = !empty($get['pct']) ? $get['pct'] : null;
        $keyword['product']['name'] = !empty($modelProductCategory) ? $modelProductCategory['name'] : null;
        $keyword['category'] = !empty($get['ctg']) ? $get['ctg'] : null;
        $keyword['map']['coordinate'] = !empty($get['cmp']) ? $get['cmp'] : null;
        $keyword['map']['radius'] = !empty($get['rmp']) ? $get['rmp'] : null;
        $keyword['facility'] = !empty($get['fct']) ? $get['fct'] : null;
        $keyword['price']['min'] = ($keyword['searchType'] == \Yii::t('app', 'favorite') || $keyword['searchType'] == \Yii::t('app', 'online-order')) && $get['pmn'] !== null && $get['pmn'] !== '' ? $get['pmn'] : null;
        $keyword['price']['max'] = ($keyword['searchType'] == \Yii::t('app', 'favorite') || $keyword['searchType'] == \Yii::t('app', 'online-order')) && $get['pmx'] !== null && $get['pmx'] !== '' ? $get['pmx'] : null;

        \Yii::$app->session->set('keyword', $get);

        return $this->render($fileRender, [
            'keyword' => $keyword,
            'params' => $get
        ]);
    }
}
