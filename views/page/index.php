<?php

use sycomponent\Tools;
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
    <div class="titan-caption">
        <div class="caption-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-12">

                        <?= $appComponent->search([
                            'keyword' => $keyword,
                        ]); ?>
                        
                    </div>
                </div>
                <div class="row mt-40">
                    <div class="col-sm-10 col-sm-offset-1">
                        <a class="section-scroll text-center text-white" href="#recent-activity">
                            <i class="fa fa-angle-double-down fa-4x animate-bounce"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <h5 class="font-alt">
                        	<a class="section-scroll text-center text-white" href="#recent-activity" style="background-color: rgba(0, 0, 0, 0.5)">
                            	<?= Yii::t('app', 'Recent Activity') ?>
                        	</a>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="module-extra-small in-result bg-main">
    <div class="container detail">
        <div class="view">
        	
        	<div class="row mt-10 mb-20">
                <div class="col-lg-12 font-alt"><?= Yii::t('app', 'News And Promo'); ?></div>
            </div>
        	
        	<div class="row mt-20">
            	<div class="col-sm-12">
                    <div class="news-promo-section owl-carousel owl-theme">
                    
                    	<?php
                    	if (!empty($modelPromo)) {
                    	    
                    	    //foreach ($modelPromo as $dataPromo) {
                    	        
                    	        $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'katalogkuliner-Dominos-Pizza-Promo-Diskon-40-Untuk-Semua-Pizza-Mulai-Dari-Rp.-25.091-.jpg', 350, 154);
                    	        
                    	        echo Html::a(Html::img($img), ['action/claim-promo'], [
                    	            'class' => 'claim-promo-btn',
                    	            'data-promo' => $modelPromo[0]['id'],
                    	            'data-date_start' => $modelPromo[0]['date_start'],
                    	            'data-date_end' => $modelPromo[0]['date_end']
                    	        ]);
                    	        
                    	        $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'LUSX8st5ITScy6ZS-promo-diskon-50-dari-zippy-1509384752_1.jpg', 350, 154);
                    	        
                    	        echo Html::a(Html::img($img), ['action/claim-promo'], [
                    	            'class' => 'claim-promo-btn',
                    	            'data-promo' => $modelPromo[1]['id'],
                    	            'data-date_start' => $modelPromo[1]['date_start'],
                    	            'data-date_end' => $modelPromo[1]['date_end']
                    	        ]);
                    	    //}
                    	} ?>
                    
                    	<?= Html::a(Html::img('https://play.google.com/intl/en_us/badges/images/generic/id_badge_web_generic.png'), 'https://play.google.com/store/apps/details?id=com.asikmakan.app') ?>
                    	<?= Html::a(Html::img('https://placehold.it/360x154&text=1', ['class' => 'img-responsive']), 'https://asikmakan.com') ?>
                    	
                    </div>
                </div>
            </div>

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

<div id="temp-listview-recent-post" class="hidden">

</div>

<?php
GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
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
    })
    
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

    $(".claim-promo-btn").on("click", function() {
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "promo_id": $(this).data("promo")
            },
            url: $(this).attr("href"),
            success: function(response) {
                
                messageResponse(response.icon, response.title, response.message, response.type);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>