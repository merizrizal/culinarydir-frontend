<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use frontend\components\AppComponent;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $filter array */

dosamigos\gallery\GalleryAsset::register($this);
dosamigos\gallery\DosamigosAsset::register($this);

$this->title = Yii::t('app', 'Search Result') . ' - Map';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$appComponent = new AppComponent(); ?>

<div class="main">

    <section class="module-small result-map-search" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-result-bg.jpeg' ?>">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <?= $appComponent->searchPopover([
                        'keyword' => $keyword,
                        'popover' => false,
                    ]); ?>

                </div>
            </div>
        </div>
    </section>

    <section class="module-small-map bg-main">
        <div class="row">

            <?php
            $urlResultList = ArrayHelper::merge(['result-list'], $filter); 
            $btnResultList = Html::a('<i class="fa fa-list"></i> List', $urlResultList, ['class' => 'btn btn-round btn-default btn-list']);
            $btnResultMap = Html::a('<i class="fa fa-location-arrow"></i> ' . Yii::t('app', 'Map'), '', ['class' => 'btn btn-round btn-d btn-map']); ?>

            <div class="col-md-7 col-sm-7 col-xs-7 mt-10 mb-10 visible-lg visible-md visible-sm text-right">

                <?= $btnResultList ?>
                <?= $btnResultMap ?>

            </div>
            <div class="col-tab-7 mt-10 mb-10 visible-tab text-center">

                <?= $btnResultList ?>
                <?= $btnResultMap ?>

            </div>
            <div class="col-xs-7 mt-10 mb-10 visible-xs text-center">

                <?= $btnResultList ?>
                <?= $btnResultMap ?>

            </div>
            <div class="col-md-5 col-sm-5 col-tab-5 col-xs-5 mt-10 mb-10">
                <div class="visible-lg visible-md visible-sm">

                    <?= $appComponent->searchPopover([
                        'keyword' => $keyword,
                        'popover' => true,
                    ]); ?>

                </div>

                <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-map-toggle visible-tab']) ?>
                <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-map-toggle visible-xs']) ?>

            </div>
        </div>

        <div class="row">
            <div class="col-md-7 col-sm-7 col-xs-12">
                <div id="maps"></div>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12">
                <section class="module-extra-small-map in-result bg-main">
                    <div class="result-map"></div>
                </section>
            </div>
        </div>
    </section>
</div>

<?= $appComponent->searchJsComponent(); ?>

<?php
frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC84sFxZL4KCPIFl8ezsta45Rm8WPRIM7Y', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".result-map-search").hide();    

    $.ajax({
        cache: false,
        type: "GET",
        data: ' . Json::encode(Yii::$app->request->get()) . ',
        url: "' . Yii::$app->urlManager->createUrl(['data/result-map']) . '",
        success: function(response) {

            $(".result-map").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });

    $(".btn-search-map-toggle").on("click", function() {

        $(".result-map-search").toggle();
    });

    $(".btn-map").on("click", function() {

        return false;
    });
';

$this->registerJs($jscript); ?>