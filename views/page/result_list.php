<?php

use yii\helpers\Html;
use yii\helpers\Json;
use frontend\components\AppComponent;

dosamigos\gallery\GalleryAsset::register($this);
dosamigos\gallery\DosamigosAsset::register($this);

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]);

$appComponent = new AppComponent(['showFacilityFilter' => true]); ?>

<div class="main">

    <section class="module-small visible-lg visible-md visible-sm" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-result-bg.jpeg' ?>">
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

    <section class="module-small result-list-search" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-result-bg.jpeg' ?>">
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
                    $urlResultMap = ['result-map'];
                    $newUrlResultMap = array_merge($urlResultMap, Yii::$app->request->get()); ?>

                    <?= Html::a('<i class="fa fa-list"></i> List', null, ['class' => 'btn btn-round btn-d']) ?>
                    <?= Html::a('<i class="fa fa-location-arrow"></i> Map', $newUrlResultMap, ['class' => 'btn btn-round btn-default']) ?>

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
$csscript = '
    .result-list {
        padding-top: 0;
    }
';

$this->registerCss($csscript);

frontend\components\GrowlCustom::widget();

$jscript = '
    $(".result-list-search").hide();

    $(".btn-search-toggle").on("click", function() {

        $(".result-list-search").toggle();
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