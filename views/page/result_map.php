<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use frontend\components\AppComponent;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $params array */

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
    <section class="module-small-map bg-main">
        <div class="row">

            <?php
            $urlResultList = ArrayHelper::merge(['result-list'], $params); 
            $btnResultList = Html::a('<i class="fa fa-list"></i> List', $urlResultList, ['class' => 'btn btn-round btn-default btn-list']);
            $btnResultMap = Html::a('<i class="fa fa-location-arrow"></i> ' . Yii::t('app', 'Map'), '', ['class' => 'btn btn-round btn-d btn-map']); ?>

            <div class="col-sm-7 mt-10 mb-10 visible-lg visible-md visible-sm text-right">

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
            <div class="col-xs-5 mt-10 mb-10">

                <?= $appComponent->search([
                    'keyword' => $keyword,
                    'type' => 'result-map-page'
                ]); ?>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-7 col-xs-12">
                <div id="maps"></div>
            </div>
            <div class="col-sm-5 col-xs-12">
                <section class="module-extra-small-map in-result bg-main">
                    <div class="result-map"></div>
                </section>
            </div>
        </div>
    </section>
</div>

<?php
echo $appComponent->searchJsComponent($keyword, 'map');

GrowlCustom::widget();
frontend\components\RatingColor::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDORji7AXzhxgYhuKOGJg6_KYrnTPYPOn8', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
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

    $(".btn-map").on("click", function() {

        return false;
    });

    $(".result-map").on("click", ".popover-tag", function() {

        return false;
    });
';

$this->registerJs($jscript); ?>