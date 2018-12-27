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
/* @var $modelUserLove core\models\UserLove */

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-user-love a',
    'options' => ['id' => 'pjax-user-love-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-user-love', 'class' => 'pagination'],
]); ?>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<div class="row" style="position: relative;">
    <div class="user-love-container">
    
    	<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <?php
        if (!empty($modelUserLove)):

            foreach ($modelUserLove as $dataUserLove): ?>

                <div class="col-lg-4 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user-love">
                        <div class="row">
                            <div class="col-xs-12">

                                <?php
                                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 565, 350);

                                if (!empty($dataUserLove['business']['businessImages'][0]['image'])) {

                                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataUserLove['business']['businessImages'][0]['image'], 565, 350);
                                }

                                echo Html::a(Html::img($img), ['page/detail', 'id' => $dataUserLove['business']['id']]); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h5 class="m-0">
                                                <?= Html::a($dataUserLove['business']['name'], ['page/detail', 'id' => $dataUserLove['business']['id']]); ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-9">
                                            <small class="m-0 mb-10">
                                                <?= $dataUserLove['business']['businessLocation']['village']['name'] . ', ' . $dataUserLove['business']['businessLocation']['city']['name'] ?>
                                            </small>
                                        </div>

                                        <?php
                                        if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->id == $dataUserLove['user_id']): ?>

                                            <div class="col-xs-3">
                                                <?= Html::a('<h2 class="mt-0 mb-0 text-red fas fa-heart"></h2>', ['action/submit-user-love'], ['class' => 'unlove-place', 'data-business-id' => $dataUserLove['business']['id']]) ?>
                                            </div>

                                        <?php
                                        endif; ?>

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
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
$jscript = '
    $("#pjax-user-love-container").off("pjax:send");
    $("#pjax-user-love-container").on("pjax:send", function() {

        $(".user-love-container").children(".overlay").show();
        $(".user-love-container").children(".loading-img").show();
    });

    $("#pjax-user-love-container").off("pjax:complete");
    $("#pjax-user-love-container").on("pjax:complete", function() {

        $(".user-love-container").children(".overlay").hide();
        $(".user-love-container").children(".loading-img").hide();
    });

    $("#pjax-user-love-container").off("pjax:error");
    $("#pjax-user-love-container").on("pjax:error", function (event) {

        event.preventDefault();
    });

    $(".total-user-love").html("' . $totalCount . '");
';

$this->registerJs($jscript);

Pjax::end(); ?>