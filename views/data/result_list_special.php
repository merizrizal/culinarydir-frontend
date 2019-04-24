<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use frontend\components\AddressType;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelBusinessPromo core\models\BusinessPromo */

kartik\popover\PopoverXAsset::register($this);
common\assets\OwlCarouselAsset::register($this);

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
]); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            <div class="row mt-10">
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
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 box-place">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none;"></div>

            <?php
            if (!empty($modelBusinessPromo)):

                foreach ($modelBusinessPromo as $dataBusinessPromo): ?>

                    <div class="row mb-10">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="box box-small">
                            
                            	<?= Html::hiddenInput('business_promo_id', $dataBusinessPromo['id'], ['class' => 'business-promo-id']) ?>

                                <div class="row">
                                    <div class="col-md-5 col-sm-5 col-tab-6 col-xs-12 col direct-link" data-link="<?= Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusinessPromo['business']['id'], '#' => 'special']) ?>">
                                    	<div class="result-list-special-image owl-carousel owl-theme">
                                            <?= Html::img(null, ['class' => 'owl-lazy', 'data-src' => Yii::$app->params['endPointLoadImage'] . 'business-promo?image=' . $dataBusinessPromo['image'] . '&w=567&h=319']); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-7 col-sm-7 col-tab-6 col-xs-12 col">
                                        <div class="short-desc">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="m-0">
                                                    
                                                        <?= Html::a($dataBusinessPromo['title'], [
                                                            'page/detail',
                                                            'city' => Inflector::slug($dataBusinessPromo['business']['businessLocation']['city']['name']),
                                                            'uniqueName' => $dataBusinessPromo['business']['unique_name'],
                                                            '#' => 'special'
                                                        ], [
                                                            'class' => 'link-to-business-detail'
                                                        ]); ?>
                                                        
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="m-0"><small><?= $dataBusinessPromo['business']['name'] ?></small></h4>
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <div class="widget">
                                                        <ul class="icon-list">
                                                            <li>
                                                            	<i class="aicon aicon-home1"></i>
                                                                <?= AddressType::widget(['businessLocation' => $dataBusinessPromo['business']['businessLocation']]) ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <div class="widget">
                                                        <ul class="icon-list">
                                                            <li class="tag">

                                                                <?php
                                                                $businessProductCategoryLimit = 3;
                                                                $businessProductCategoryList = '';
                                                                $businessProductCategoryPopover = '';

                                                                foreach ($dataBusinessPromo['business']['businessProductCategories'] as $i => $dataBusinessProductCategory) {
                                                                    
                                                                    if (!empty($dataBusinessProductCategory['productCategory'])) {
                                                                        
                                                                        $hashtagItem = '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                        
                                                                        if ($i < $businessProductCategoryLimit) {
    
                                                                            $businessProductCategoryList .= $hashtagItem;
                                                                        } else {
    
                                                                            $businessProductCategoryPopover .= $hashtagItem;
                                                                        }
                                                                    }
                                                                }

                                                                if (count($dataBusinessPromo['business']['businessProductCategories']) > $businessProductCategoryLimit) {

                                                                    echo Html::a($businessProductCategoryList, '#', ['id' => 'business-product-category-popover' . $dataBusinessPromo['id'], 'class' => 'popover-tag']);
                                                                } else {

                                                                    echo $businessProductCategoryList;
                                                                } ?>

                                                                <div id="business-product-category-container-popover<?= $dataBusinessPromo['id']; ?>" class="popover popover-x popover-default">
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

                    <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

                </div>
                <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

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
$jscript = '
    $(".business-promo-id").each(function() {

        $("#business-product-category-popover" + $(this).val()).popoverButton({
            trigger: "hover focus",
            placement: "bottom bottom-left",
            target: "#business-product-category-container-popover" + $(this).val()
        });
    });

    $(".direct-link").on("click", function() {

        if (!$(this).hasClass("next") && !$(this).hasClass("prev")) {

            window.location.href = $(this).parent().find(".link-to-business-detail").attr("href");
        }
    });

    $(".result-list-special-image").owlCarousel({
        lazyLoad: true,
        items: 1,
        mouseDrag: false,
        touchDrag: false
    });

    $("#pjax-result-list").off("pjax:send");
    $("#pjax-result-list").on("pjax:send", function() {

        $(".box-place").children(".overlay").show();
        $(".box-place").children(".loading-img").show();
    });

    $("#pjax-result-list").off("pjax:complete");
    $("#pjax-result-list").on("pjax:complete", function() {

        $("html, body").animate({ scrollTop: $("section.in-result").offset().top }, "slow");

        $(".box-place").children(".overlay").hide();
        $(".box-place").children(".loading-img").hide();
    });

    $("#pjax-result-list").off("pjax:error");
    $("#pjax-result-list").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>