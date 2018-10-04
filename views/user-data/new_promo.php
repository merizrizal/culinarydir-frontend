<?php

use yii\helpers\Html;
use sycomponent\Tools;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelBusinessPromo core\models\BusinessPromo */

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

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

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

<div class="row" style="position: relative;">
    <div class="new-promo-container">
    
    	<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <?php
        if (!empty($modelBusinessPromo)):

            foreach ($modelBusinessPromo as $dataBusinessPromo): ?>

                <div class="col-lg-4 col-md-6 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">

                                <?php
                                $img = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg';

                                if (!empty($dataBusinessPromo['image'])) {

                                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/business_promo/', $dataBusinessPromo['image'], 347, 210);

                                }

                                $img = Html::img($img); 
                                
                                echo Html::a($img, ['page/detail', 'id' => $dataBusinessPromo['business_id'], '#' => 'special'])?>

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

                                            <small class="m-0">
                                                <?= $dataBusinessPromo['title']; ?>
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

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

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
frontend\components\GrowlCustom::widget();

$jscript = '
    $(".total-new-promo").html("' . $totalCount . '");

    $("#pjax-new-promo-container").on("pjax:send", function() {

        $(".new-promo-container").children(".overlay").show();
        $(".new-promo-container").children(".loading-img").show();
    });

    $("#pjax-new-promo-container").on("pjax:complete", function() {

        $(".new-promo-container").children(".overlay").hide();
        $(".new-promo-container").children(".loading-img").hide();
    });

    $("#pjax-new-promo-container").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>