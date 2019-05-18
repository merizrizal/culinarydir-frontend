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
/* @var $modelUserVisit core\models\UserVisit */

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
    <div class="user-visit-container">

    	<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <?php
        if (!empty($modelUserVisit)):

            foreach ($modelUserVisit as $dataUserVisit):

                $urlBusinessDetail = [
                    'page/detail',
                    'city' => Inflector::slug($dataUserVisit['business']['businessLocation']['city']['name']),
                    'uniqueName' => $dataUserVisit['business']['unique_name']
                ]; ?>

                <div class="col-lg-4 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user">
                        <div class="row">
                            <div class="col-xs-12">

                                <?php
                                $img = (!empty($dataUserVisit['business']['businessImages']) ? $dataUserVisit['business']['businessImages'][0]['image'] : '') . '&w=565&h=350';
                                echo Html::a(Html::img(\Yii::$app->params['endPointLoadImage'] . 'registry-business?image=' . $img), $urlBusinessDetail); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h5 class="m-0">
                                                <?= Html::a($dataUserVisit['business']['name'], $urlBusinessDetail); ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-xs-12">
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
    $("#pjax-user-visit-container").off("pjax:send");
    $("#pjax-user-visit-container").on("pjax:send", function() {

        $(".user-visit-container").children(".overlay").show();
        $(".user-visit-container").children(".loading-img").show();
    });

    $("#pjax-user-visit-container").off("pjax:complete");
    $("#pjax-user-visit-container").on("pjax:complete", function() {

        $(".user-visit-container").children(".overlay").hide();
        $(".user-visit-container").children(".loading-img").hide();
    });

    $("#pjax-user-visit-container").off("pjax:error");
    $("#pjax-user-visit-container").on("pjax:error", function (event) {

        event.preventDefault();
    });

    $(".total-user-visit").html("' . $totalCount . '");
';

$this->registerJs($jscript);

Pjax::end(); ?>