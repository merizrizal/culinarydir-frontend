<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
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

        <?= \Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

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

            foreach ($modelBusinessPromo as $dataBusinessPromo):

                $urlBusinessDetail = [
                    'page/detail',
                    '#' => 'special',
                    'city' => Inflector::slug($dataBusinessPromo['business']['businessLocation']['city']['name']),
                    'uniqueName' => $dataBusinessPromo['business']['unique_name']
                ]; ?>

                <div class="col-lg-4 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user">
                        <div class="row">
                            <div class="col-xs-12">

                                <?php
                                $img = (!empty($dataBusinessPromo['image']) ? $dataBusinessPromo['image'] : '') . '&w=565&h=350';
                                echo Html::a(Html::img(\Yii::$app->params['endPointLoadImage'] . 'business-promo?image=' . $img), $urlBusinessDetail); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h5 class="m-0">
                                                <?= Html::a($dataBusinessPromo['business']['name'], $urlBusinessDetail); ?>
                                            </h5>
										</div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-xs-12">
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

        <?= \Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
$jscript = '
    $(".total-new-promo").html("' . $totalCount . '");

    $("#pjax-new-promo-container").off("pjax:send");
    $("#pjax-new-promo-container").on("pjax:send", function() {

        $(".new-promo-container").children(".overlay").show();
        $(".new-promo-container").children(".loading-img").show();
    });

    $("#pjax-new-promo-container").off("pjax:complete");
    $("#pjax-new-promo-container").on("pjax:complete", function() {

        $(".new-promo-container").children(".overlay").hide();
        $(".new-promo-container").children(".loading-img").hide();
    });

    $("#pjax-new-promo-container").off("pjax:error");
    $("#pjax-new-promo-container").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>