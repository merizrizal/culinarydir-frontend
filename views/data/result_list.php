<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use frontend\components\AddressType;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelBusiness core\models\Business */

kartik\popover\PopoverXAsset::register($this);

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-result-list a',
    'options' => ['id' => 'pjax-result-list'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-result-list', 'class' => 'pagination'],
]);

$jspopover = ''; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            <div class="row mt-10">
                <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

                    <?= Yii::t('app', 'Showing ') . $startItem . ' - ' . $endItem . Yii::t('app', ' OF ') . $totalCount . ' ' . Yii::t('app', 'Results'); ?>

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
        </div>
    </div>
</div>

<div class="container">
    <div class="row">

        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 box-place">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none;"></div>

            <?php
            if (!empty($modelBusiness)):

                foreach ($modelBusiness as $dataBusiness):

                    $jspopover .= '
                        $("#business-product-category-popover' . $dataBusiness['id'] . '").popoverButton({
                            trigger: "hover focus",
                            placement: "bottom bottom-left",
                            target: "#business-product-category-container-popover' . $dataBusiness['id'] . '"
                        });
                    '; ?>

                        <div class="row mb-10">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="box box-small">

                                    <div class="row">
                                        <div class="col-md-5 col-sm-5 col-tab-7 col-xs-7 col direct-link" data-link="<?= Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusiness['id']]) ?>">

                                            <?php
                                            if (count($dataBusiness['businessImages']) > 1) {

                                                $images = [];

                                                foreach ($dataBusiness['businessImages'] as $dataBusinessImage) {

                                                    $href = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg';

                                                    if (!empty($dataBusinessImage['image'])) {

                                                        $href = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataBusinessImage['image'], 490, 276);

                                                    }

                                                    $images[] = [
                                                        'title' => '',
                                                        'href' => $href,
                                                        'type' => 'image/jpeg',
                                                        'poster' => $href,
                                                    ];
                                                }

                                                echo dosamigos\gallery\Carousel::widget([
                                                    'items' => $images,
                                                    'json' => true,
                                                    'templateOptions' => ['id' => 'blueimp-gallery-' . $dataBusiness['id']],
                                                    'clientOptions' => ['container' => '#blueimp-gallery-' . $dataBusiness['id']],
                                                    'options' => ['id' => 'blueimp-gallery-' . $dataBusiness['id']],
                                                ]);
                                            } else {

                                                $image = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg';

                                                if (!empty($dataBusiness['businessImages'][0]['image'])) {

                                                    $image = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataBusiness['businessImages'][0]['image'], 490, 276);
                                                }

                                                echo Html::img($image);
                                            } ?>

                                        </div>

                                        <?php
                                        $classLove = !empty($dataBusiness['userLoves'][0]) ? 'fas fa-heart' : 'far fa-heart';
                                        $vote_value = !empty($dataBusiness['businessDetail']['vote_value']) ? $dataBusiness['businessDetail']['vote_value'] : 0;
                                        $voters = !empty($dataBusiness['businessDetail']['voters']) ? $dataBusiness['businessDetail']['voters'] : 0;
                                        
                                        $layoutRatings = '
                                            <div class="love love-' . $dataBusiness['id'] . '">
                                                <h2 class="mt-0 mb-20 text-red"><span class="' . $classLove . ' love-button" data-id="' . $dataBusiness['id'] . '"></span></h2>
                                            </div>
                                            <div class="rating rating-top">
                                                <h2 class="mt-0 mb-0"><span class="label label-success pt-10">' . number_format($vote_value, 1) . '</span></h2>' .
                                                Yii::t('app', '{value, plural, =0{# Vote} =1{# Vote} other{# Votes}}', ['value' => $voters]) . '
                                            </div>
                                        '; ?>

                                        <div class="col-tab-5 col visible-tab text-center">
                                            
                                            <?= $layoutRatings ?>
                                            
                                        </div>

                                        <div class="col-xs-5 col visible-xs text-center">
                                            
                                            <?= $layoutRatings?>
                                            
                                        </div>

                                        <div class="col-md-7 col-sm-7 col-xs-12 col">
                                            <div class="short-desc">
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12 col">
                                                        <h4 class="font-alt m-0">

                                                            <?= Html::a($dataBusiness['name'], ['page/detail', 'id' => $dataBusiness['id']]); ?>

                                                        </h4>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-10 col-sm-10 col-xs-12 col">
                                                        <h4 class="m-0">

                                                            <?php
                                                            $categories = '';
                                                            foreach ($dataBusiness['businessCategories'] as $dataBusinessCategories) {

                                                                $categories .= $dataBusinessCategories['category']['name'] . ' / ';
                                                            } ?>

                                                            <small class="mt-10"><?= trim($categories, ' / ') ?></small>

                                                        </h4>
                                                        <div class="widget">
                                                            <ul class="icon-list">
                                                                <li>
                                                                    <i class="aicon aicon-home"></i>

                                                                    <?= AddressType::widget([
                                                                        'addressType' => $dataBusiness['businessLocation']['address_type'],
                                                                        'address' => $dataBusiness['businessLocation']['address']
                                                                    ]) ?>

                                                                </li>
                                                                <li>
                                                                    <i class="aicon aicon-rupiah"></i>

                                                                    <?php
                                                                    if (!empty($dataBusiness['businessDetail']['price_min']) && !empty($dataBusiness['businessDetail']['price_max'])) {

                                                                        echo Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']) . ' - ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);
                                                                    } else if (empty($dataBusiness['businessDetail']['price_min']) && !empty($dataBusiness['businessDetail']['price_max'])) {

                                                                        echo Yii::t('app', 'Under') . ' ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);
                                                                    } else if (empty($dataBusiness['businessDetail']['price_max']) && !empty($dataBusiness['businessDetail']['price_min'])) {

                                                                        echo Yii::t('app', 'Above') . ' ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']);
                                                                    } else {

                                                                        echo '-';
                                                                    } ?>

                                                                </li>
                                                                <li class="tag">

                                                                    <?php
                                                                    $businessProductCategoryLimit = 3;
                                                                    $businessProductCategoryList = '';
                                                                    $businessProductCategoryPopover = '';

                                                                    foreach($dataBusiness['businessProductCategories'] as $key => $dataBusinessProductCategory){

                                                                        if ($key < $businessProductCategoryLimit) {

                                                                            $businessProductCategoryList .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                        } else {

                                                                            $businessProductCategoryPopover .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                        }
                                                                    }

                                                                    if (count($dataBusiness['businessProductCategories']) > $businessProductCategoryLimit) {

                                                                        echo Html::a($businessProductCategoryList, '#', ['id' => 'business-product-category-popover' . $dataBusiness['id'], 'class' => 'popover-tag']);
                                                                    } else {

                                                                        echo $businessProductCategoryList;
                                                                    } ?>

                                                                    <div id="business-product-category-container-popover<?= $dataBusiness['id']; ?>" class="popover popover-x popover-default">
                                                                        <div class="arrow mt-0"></div>
                                                                        <div class="popover-body popover-content">
                                                                            <div class="row">
                                                                                <div class="col-sm-12 col-xs-12">

                                                                                    <?= $businessProductCategoryPopover; ?>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 col-sm-2 col visible-lg visible-md visible-sm text-center">
                                                        <div class="love love-<?= $dataBusiness['id'] ?> pull-right">
                                                            <h2 class="mt-0 mb-20 text-red"><span class="<?= $classLove ?> love-button" data-id="<?= $dataBusiness['id'] ?>"></span></h2>
                                                        </div>
                                                        <div class="rating pull-right">
                                                            <h2 class="mt-0 mb-0"><span class="label label-success pt-10"><?= number_format($vote_value, 1) ?></span></h2>
                                                            <?= Yii::t('app', '{value, plural, =0{# Vote} =1{# Vote} other{# Votes}}', ['value' => $voters]) ?>
                                                        </div>
                                                    </div>
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
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            <div class="row mt-10">
                <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

                    <?= Yii::t('app', 'Showing ') . $startItem . ' - ' . $endItem . Yii::t('app', ' OF ') . $totalCount . ' ' . Yii::t('app', 'Results'); ?>

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
        </div>
    </div>
</div>

<?php
$csscript ='
    .widget .icon-list li a::before {
        content: none;
    }

    a.popover-tag:hover,
    a.popover-tag:focus {
        color: #111;
    }
';

$this->registerCss($csscript);

frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();

$jscript = '
    $("#pjax-result-list").on("pjax:send", function() {
        $(".box-place").children(".overlay").show();
        $(".box-place").children(".loading-img").show();
    });

    $("#pjax-result-list").on("pjax:complete", function() {
        $(".box-place").children(".overlay").hide();
        $(".box-place").children(".loading-img").hide();
    });

    $("#pjax-result-list").on("pjax:error", function (event) {
        event.preventDefault();
    });

    $(".popover-tag").on("click", function() {
        return false;
    });

    $(".love-button").on("click", function() {

        var business_id = $(this).data("id");

        $.ajax({
            cache: false,
            url: "'. Yii::$app->urlManager->createUrl('action/submit-user-love').'",
            type: "POST",
            data: {
                "business_id": business_id
            },
            success: function(response) {

                if (response.success) {

                    if (response.is_active) {
                        $(".love-" + business_id).find("span.love-button").removeClass("far fa-heart").addClass("fas fa-heart");

                    } else {
                        $(".love-" + business_id).find("span.love-button").removeClass("fas fa-heart").addClass("far fa-heart");
                    }
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });

    ratingColor($(".rating"), "span");
';

$this->registerJs($jscript . $jspopover);

Pjax::end(); ?>