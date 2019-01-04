<?php

use yii\widgets\ListView;
use yii\widgets\LinkPager;
use yii\web\View;
use frontend\components\AppComponent;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $dataProviderUserPostMain yii\data\ActiveDataProvider */
/* @var $keyword array */

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

$appComponent = new AppComponent(); 

$info = '
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-sm-12">
            <div class="titan-title-tagline p-10 mb-10" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 5px;">650+ tempat kuliner dan terus bertambah</div>
        </div>
    </div>
';

if (Yii::$app->request->getUserAgent() != 'com.asikmakan.app') {
    
    $info .= '
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12">
                <div class="p-10 mb-10" style="background: rgba(229, 38, 38, 0.9); border-radius: 5px;">
                    <a href="https://play.google.com/store/apps/details?id=com.asikmakan.app" style="color:#fff; padding:0" class="btn btn-standard"><i class="aicon aicon-mobile"></i> Download app <strong>Asikmakan</strong> di <strong>Google Play Store</strong></a>
                </div>
            </div>
        </div>
    ';
} ?>

<section class="home-section home-full-height bg-dark visible-lg visible-md visible-sm" data-background="<?= $background ?>">
    <div class="titan-caption">
        <div class="caption-content">
            <div class="container">
                <?= $info ?>
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

<section class="module-small visible-tab" data-background="<?= $background ?>">
    <div class="container">
        <?= $info ?>
        <div class="row">
            <div class="col-tab-12">

                <?= $appComponent->search([
                    'keyword' => $keyword,
                    'id' => 'tab-search'
                ]); ?>

            </div>
        </div>
    </div>
</section>

<section class="module-small visible-xs" data-background="<?= $background ?>">
    <div class="container">
        <?= $info ?>
        <div class="row">
            <div class="col-xs-12">

                <?= $appComponent->search([
                    'keyword' => $keyword,
                    'id' => 'xs-search'
                ]); ?>

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

<?= $appComponent->searchJsComponent(); ?>

<div id="temp-listview-recent-post" class="hidden">

</div>

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
';

$this->registerJs($jscript); ?>