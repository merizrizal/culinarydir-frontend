<?php

use yii\helpers\Html;
use sycomponent\Tools;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-user-visit a',
    'options' => ['id' => 'pjax-user-visit-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-user-visit', 'class' => 'pagination'],
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
    <div class="user-visit-container">

        <?php
        if (!empty($modelUserVisit)):

            foreach ($modelUserVisit as $dataUserVisit): ?>

                <div class="col-lg-4 col-md-6 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <a href="<?= Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataUserVisit['business']['id']]) ?>">

                                    <?= Html::img(!empty($dataUserVisit['business']['businessImages'][0]['image']) ? Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataUserVisit['business']['businessImages'][0]['image'], 347.333, 210.283) : Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg', ['class' => 'img-responsive img-component']); ?>

                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">
                                            <h4 class="font-alt m-0">

                                                <?= Html::a($dataUserVisit['business']['name'], ['page/detail', 'id' => $dataUserVisit['business']['id']]); ?>

                                            </h4>

                                            <small class="m-0">

                                                <?= $dataUserVisit['business']['businessLocation']['village']['name'] . ', ' . $dataUserVisit['business']['businessLocation']['city']['name'] ?>

                                            </small>
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

$jscript = '
    $("#pjax-user-visit-container").on("pjax:send", function() {
        $(".user-visit-container").parent().siblings(".overlay").show();
        $(".user-visit-container").parent().siblings(".loading-img").show();
    });

    $("#pjax-user-visit-container").on("pjax:complete", function() {
        $(".user-visit-container").parent().siblings(".overlay").hide();
        $(".user-visit-container").parent().siblings(".loading-img").hide();
    });

    $("#pjax-user-visit-container").on("pjax:error", function (event) {
        event.preventDefault();
    });

    $(".total-user-visit").html("' . $totalCount . '");
';

$this->registerJs($jscript);

Pjax::end(); ?>