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
    <div class="user-love-container">
    
    	<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <?php
        if (!empty($modelUserLove)):

            foreach ($modelUserLove as $dataUserLove): ?>

                <div class="col-lg-4 col-md-6 col-sm-6 col-tab-6 col-xs-12 mb-10">
                    <div class="box user">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">

                                <?php
                                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 347, 210);

                                if (!empty($dataUserLove['business']['businessImages'][0]['image'])) {

                                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataUserLove['business']['businessImages'][0]['image'], 347, 210);
                                }
                                
                                $img = Html::img($img);

                                echo Html::a($img, ['page/detail', 'id' => $dataUserLove['business']['id']]); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="short-desc">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-tab-12 col-xs-12">
                                            <h4 class="font-alt m-0">
                                                <?= Html::a($dataUserLove['business']['name'], ['page/detail', 'id' => $dataUserLove['business']['id']]); ?>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-9 col-md-10 col-sm-10 col-tab-9 col-xs-9">
                                            <small class="m-0 mb-10">
                                                <?= $dataUserLove['business']['businessLocation']['village']['name'] . ', ' . $dataUserLove['business']['businessLocation']['city']['name'] ?>
                                            </small>
                                        </div>

                                        <?php
                                        if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->id == $dataUserLove['user_id']): ?>

                                            <div class="col-lg-3 col-md-2 col-sm-2 col-tab-3 col-xs-3">

                                                <?= Html::hiddenInput('business_id', $dataUserLove['business']['id'], ['class' => 'business-id']) ?>

                                                <div class="widget">
                                                    <ul class="heart-rating">
                                                        <li>
                                                            <?= Html::a('<h2 class="mt-0 mb-0 text-red fas fa-heart"></h2>', ['action/submit-user-love'], ['class' => 'unlove-place']) ?>
                                                        </li>
                                                    </ul>
                                                </div>
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
    .widget .icon-list li a::before {
        content: none;
    }
';

$this->registerCss($csscript);

$jscript = '
    $(".business-id").each(function() {

        var thisObj = $(this);

        thisObj.parent().find(".unlove-place").on("click", function() {

            $.ajax({
                cache: false,
                url: $(this).attr("href"),
                type: "POST",
                data: {
                    "business_id": thisObj.val()
                },
                success: function(response) {

                    if (response.success) {
                        
                        var count = parseInt($(".total-user-love").html());

                        if (response.is_active) {
                            
                            thisObj.parent().find(".unlove-place").html("<h2 class=\"mt-0 mb-0 text-red fas fa-heart\"></h2>");
                            $(".total-user-love").html(count + 1);
                        } else {
                            
                            thisObj.parent().find(".unlove-place").html("<h2 class=\"mt-0 mb-0 text-red far fa-heart\"></h2>");
                            $(".total-user-love").html(count - 1);
                        }
                    } else {
                        
                        messageResponse(response.icon, response.title, response.message, response.type);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    
                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                }
            });

            return false;
        });
    });

    $("#pjax-user-love-container").on("pjax:send", function() {

        $(".user-love-container").children(".overlay").show();
        $(".user-love-container").children(".loading-img").show();
    });

    $("#pjax-user-love-container").on("pjax:complete", function() {

        $(".user-love-container").children(".overlay").hide();
        $(".user-love-container").children(".loading-img").hide();
    });

    $("#pjax-user-love-container").on("pjax:error", function (event) {

        event.preventDefault();
    });

    $(".total-user-love").html("' . $totalCount . '");
';

$this->registerJs($jscript);

Pjax::end(); ?>