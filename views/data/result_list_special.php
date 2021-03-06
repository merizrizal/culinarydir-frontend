<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use frontend\components\AddressType;

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
        </div>
    </div>
</div>

<hr class="divider-w mt-10 mb-20">

<div class="container">
    <div class="row">

        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 box-place">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none"></div>

            <?php
            if (!empty($modelBusinessPromo)):

                foreach ($modelBusinessPromo as $dataBusinessPromo):

                    $jspopover .= '
                        $("#business-product-category-popover' . $dataBusinessPromo['id'] . '").popoverButton({
                            trigger: "hover focus",
                            placement: "bottom bottom-left",
                            target: "#business-product-category-container-popover' . $dataBusinessPromo['id'] . '"
                        });
                    '; ?>

                    <div class="row mb-10">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="box box-small">

                                <div class="row">
                                    <div class="col-md-5 col-sm-5 col-tab-6 col-xs-12 col direct-link" data-link="<?= Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusinessPromo['business']['id'], '#' => 'special']) ?>">

                                        <?php
                                        if (!empty($dataBusinessPromo['image'] && file_exists(Yii::getAlias('@uploads') . '/img/business_promo/' . $dataBusinessPromo['image']))) {

                                            echo Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/business_promo/', $dataBusinessPromo['image'], 490, 276), ['class' => 'img-responsive img-component']);

                                        } else {

                                            echo Html::img(Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg', ['class' => 'img-responsive img-component']);

                                        } ?>

                                    </div>

                                    <div class="col-md-7 col-sm-7 col-tab-6 col-xs-12 col">
                                        <div class="short-desc">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="font-alt m-0">

                                                        <?= Html::a($dataBusinessPromo['title'], ['page/detail', 'id' => $dataBusinessPromo['business']['id'], '#' => 'special']); ?>

                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="m-0"><small><?= $dataBusinessPromo['business']['name'] ?></small></h4>
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-md-12 col">
                                                    <div class="widget">
                                                        <ul class="icon-list">
                                                            <li><i class="aicon aicon-home"></i>

                                                                <?= AddressType::widget([
                                                                    'addressType' => $dataBusinessPromo['business']['businessLocation']['address_type'],
                                                                    'address' => $dataBusinessPromo['business']['businessLocation']['address']
                                                                ]) ?>

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-md-12 col">
                                                    <div class="widget">
                                                        <ul class="icon-list">
                                                            <li class="tag">

                                                                <?php
                                                                $businessProductCategoryLimit = 3;
                                                                $businessProductCategoryList = '';
                                                                $businessProductCategoryPopover = '';
                                                                $businessProductCategoryHref = '<a href="#" id="business-product-category-popover' . $dataBusinessPromo['id'] . '" class="popover-tag">';

                                                                foreach ($dataBusinessPromo['business']['businessProductCategories'] as $key => $dataBusinessProductCategory) {

                                                                    if ($key < $businessProductCategoryLimit) {

                                                                        $businessProductCategoryList .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                    } else if ($key > $businessProductCategoryLimit - 1) {

                                                                        $businessProductCategoryPopover .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                    }
                                                                }

                                                                echo (count($dataBusinessPromo['business']['businessProductCategories']) > $businessProductCategoryLimit ? $businessProductCategoryHref . $businessProductCategoryList . '</a>' : $businessProductCategoryList); ?>

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

<hr class="divider-w mt-20 mb-10">

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            <div class="row mt-10">
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
';

$this->registerJs($jscript . $jspopover);

Pjax::end(); ?>