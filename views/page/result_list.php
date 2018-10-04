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

$this->title = Yii::t('app', 'Search Result');

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$appComponent = new AppComponent(['showFacilityFilter' => true]);

$background = Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-result-bg.jpeg'; ?>

<div class="main">

    <section class="module-small visible-lg visible-md visible-sm" data-background="<?= $background ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12">

                    <?= $appComponent->search([
                        'keyword' => $keyword,
                    ]); ?>

                </div>
            </div>
        </div>
    </section>

    <section class="module-small result-list-search" data-background="<?= $background ?>">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <?= $appComponent->search([
                        'keyword' => $keyword,
                        'id' => 'search'
                    ]); ?>

                </div>
            </div>
        </div>
    </section>

    <section class="module-extra-small in-result bg-main">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-7">

                    <?php
                    $urlResultMap = ArrayHelper::merge(['result-map'], $filter); ?>

                    <?= Html::a('<i class="fa fa-list"></i> List', '', ['class' => 'btn btn-round btn-d btn-list']) ?>
                    <?= Html::a('<i class="fa fa-location-arrow"></i> Map', $urlResultMap, ['class' => 'btn btn-round btn-default btn-map']) ?>

                </div>
                <div class="col-xs-5">

                    <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-toggle visible-tab']) ?>
                    <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-toggle visible-xs']) ?>

                </div>
            </div>
        </div>

        <div class="result-list"></div>
    </section>
</div>

<?= $appComponent->searchJsComponent(); ?>

<?php
frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();

$jscript = '
    $(".result-list-search").hide();

    $(".btn-search-toggle").on("click", function() {

        $(".result-list-search").toggle();
    });

    $(".btn-list").on("click", function() {

        return false;
    });

    $.ajax({
        cache: false,
        type: "GET",
        data: ' . Json::encode(Yii::$app->request->get()) . ',
        url: "' . Yii::$app->urlManager->createUrl(['data/result-list']) . '",
        success: function(response) {

            $(".result-list").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });
';

$this->registerJs($jscript); ?>