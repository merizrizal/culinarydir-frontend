<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\LinkPager;
use yii\web\View;
use frontend\components\AppComponent;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $dataProviderUserPostMain yii\data\ActiveDataProvider */
/* @var $keyword array */
/* @var $modelPromo core\models\Promo */

common\assets\OwlCarouselAsset::register($this);

$this->title = 'Home';

$background = Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$this->registerMetaTag([
    'property' => 'og:url',
    'content' => Yii::$app->urlManager->createAbsoluteUrl('')
]);

$this->registerMetaTag([
    'property' => 'og:type',
    'content' => 'website'
]);

$this->registerMetaTag([
    'property' => 'og:title',
    'content' => 'Asikmakan'
]);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$this->registerMetaTag([
    'property' => 'og:image',
    'content' => Yii::$app->urlManager->getHostInfo() . $background
]);

$appComponent = new AppComponent(); ?>

<section class="module-small bg-dark" data-background="<?= $background ?>">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12">

                <?= $appComponent->search([
                    'keyword' => $keyword,
                ]); ?>
                
            </div>
        </div>
        <div class="row mt-30">
            <div class="col-xs-12 font-alt"><strong><?= Yii::t('app', 'News And Promo'); ?></strong></div>
        </div>
    	
    	<div class="row mt-10">
        	<div class="col-xs-12">
                <div class="news-promo-section owl-carousel owl-theme">
                
                	<?php
                	if (!empty($modelPromo)) {
                	    
                	    foreach ($modelPromo as $dataPromo) {
                	        
                	        echo Html::a(Html::img(null, ['class' => 'owl-lazy', 'data-src' => Yii::$app->params['endPointLoadImage'] . 'promo?image=' . $dataPromo['image'] . '&w=350&h=154']), [
                	            'page/detail-promo',
                	            'id' => $dataPromo['id']
                	        ]);
                	    }
                	}
                
                	echo Html::a(Html::img(null, ['class' => 'owl-lazy', 'data-src' => 'https://play.google.com/intl/en_us/badges/images/generic/id_badge_web_generic.png']), 'https://play.google.com/store/apps/details?id=com.asikmakan.app'); ?>
                	
                </div>
            </div>
        </div>
    </div>
</section>

<section class="module-extra-small in-result bg-main">
    <div class="container detail">
        <div class="view">
            <div class="row mt-10 mb-20">
                <div class="col-lg-12 font-alt"><?= Yii::t('app', 'Recent Activity'); ?></div>
            </div>

            <?= ListView::widget([
                'id' => 'recent-activity',
                'dataProvider' => $dataProviderUserPostMain,
                'itemView' => '@frontend/views/data/_recent_post',
                'layout' => '
                    <div class="row">
                        {items}
                        <div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 align-center">{pager}</div>
                        </div>
                    </div>
                ',
                'pager' => [
                    'class' => LinkPager::class,
                    'maxButtonCount' => 0,
                    'prevPageLabel' => false,
                    'nextPageLabel' => Yii::t('app', 'Load More'),
                    'options' => ['id' => 'pagination-recent-post', 'class' => 'pagination'],
                ]
            ]); ?>

        </div>
    </div>
</section>

<?= $appComponent->searchJsComponent($keyword); ?>

<div id="temp-listview-recent-post" class="hidden"></div>

<?php
GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $("#recent-activity").on("click", "#pagination-recent-post li.next a", function() {

        var thisObj = $(this);
        var thisText = thisObj.html();

        $.ajax({
            cache: false,
            type: "GET",
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {
                thisObj.html("Loading...");
            },
            success: function(response) {

                $("#temp-listview-recent-post").html(response);

                $("#temp-listview-recent-post").find("#recent-activity").children(".row").children("div").each(function() {
                    $("#recent-activity").children(".row").append($(this));
                });

                thisObj.parent().parent().parent().parent().remove();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                thisObj.html(thisText);
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });

    $(".news-promo-section").owlCarousel({
        margin: 10,
        lazyLoad: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
';

$this->registerJs($jscript); ?>