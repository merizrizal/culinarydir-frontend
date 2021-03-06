<?php

use yii\helpers\Html;
use sycomponent\Tools;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-new-promo a',
    'options' => ['id' => 'pjax-new-promo-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-new-promo', 'class' => 'pagination'],
]); ?>

<div class="overlay" style="display: none;"></div>
<div class="loading-img" style="display: none"></div>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= 'Showing ' . $startItem . ' - ' . $endItem . ' of ' . $totalCount . ' results'; ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<div class="row">
    <div class="new-promo-container">

        <?php
        if (!empty($modelBusinessPromo)):

            foreach ($modelBusinessPromo as $dataBusinessPromo): ?>

                <div class="col-lg-4 col-md-6 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <a href="<?= Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusinessPromo['business_id'], '#' => 'special']) ?>">

                                    <?php
                                    if (!empty($dataBusinessPromo['image']) && file_exists(Yii::getAlias('@uploads') . '/img/business_promo/' . $dataBusinessPromo['image'])) {

                                        echo Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/business_promo/', $dataBusinessPromo['image'], 347.333, 210.283));

                                    } else {

                                        echo Html::img(Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg', ['class' => 'img-responsive img-component']);

                                    } ?>

                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">
                                            <h4 class="font-alt m-0">

                                                <?= Html::a($dataBusinessPromo['business']['name'], ['page/detail', 'id' => $dataBusinessPromo['business_id'], '#' => 'special']); ?>

                                            </h4>

                                            <h5 class="m-0">

                                                <?= $dataBusinessPromo['title']; ?>

                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
            endforeach;
        endif; ?>

    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= 'Showing ' . $startItem . ' - ' . $endItem . ' of ' . $totalCount . ' results'; ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
$csscript = '
    .pagination {
        margin: 0;
    }

    .pagination > .disabled > span {
        display: none;
    }

    .pagination > li > a {
        color: #000;
        border: 1px solid #ddd;
    }

    .pagination > .active > a {
        z-index: 3;
        color: #fff;
        cursor: default;
        background-color: rgb(229, 38, 38);
        border-color: rgb(229, 38, 38);
    }

    .pagination > .active > a:hover {
        color: #fff;
        background-color: rgb(229, 38, 38);
        border-color: rgb(229, 38, 38);
    }
';

$this->registerCss($csscript);

frontend\components\GrowlCustom::widget();

$jscript = '
    $(".total-new-promo").html("' . $totalCount . '");

    $("#pjax-new-promo-container").on("pjax:send", function() {
        $(".new-promo-container").parent().siblings(".overlay").show();
        $(".new-promo-container").parent().siblings(".loading-img").show();
    });

    $("#pjax-new-promo-container").on("pjax:complete", function() {
        $(".new-promo-container").parent().siblings(".overlay").hide();
        $(".new-promo-container").parent().siblings(".loading-img").hide();
    });

    $("#pjax-new-promo-container").on("pjax:error", function (event) {
        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>