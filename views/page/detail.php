<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use frontend\components\AppComponent;
use frontend\components\AddressType;
use sycomponent\Tools;

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'property' => 'og:url',
    'content' => Yii::$app->urlManager->createAbsoluteUrl(['page/detail', 'id' => $modelBusiness['id']])
]);

$this->registerMetaTag([
    'property' => 'og:type',
    'content' => 'website'
]);

$this->registerMetaTag([
    'property' => 'og:title',
    'content' => !empty($modelBusiness['name']) ? $modelBusiness['name'] : 'Asikmakan'
]);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' => !empty($modelBusiness['about']) ? $modelBusiness['about'] : 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]);

$this->registerMetaTag([
    'property' => 'og:image',
    'content' => Yii::$app->urlManager->createAbsoluteUrl(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $modelBusiness['businessImages'][0]['image'], 490, 276))
]);

$appComponent = new AppComponent(); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail place-detail">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?php
                    $flashKeyword = Yii::$app->session->getFlash('keyword'); ?>

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> Back to Result List', [
                        'page/result-list',
                        'special' => !empty($flashKeyword['special']) ? $flashKeyword['special'] : 0,
                        'city_id' => !empty($flashKeyword['city']) ? $flashKeyword['city'] : '',
                        'name' => !empty($flashKeyword['name']) ? $flashKeyword['name'] : '',
                        'product_category' => !empty($flashKeyword['product']['id']) ? $flashKeyword['product']['id'] : '',
                        'category_id' => !empty($flashKeyword['category']) ? $flashKeyword['category'] : '',
                        'price_min' => !empty($flashKeyword['price_min']) ? $flashKeyword['price_min'] : '',
                        'price_max' => !empty($flashKeyword['price_max']) ? $flashKeyword['price_max'] : '',
                        'coordinate_map' => !empty($flashKeyword['coordinate']) ? $flashKeyword['coordinate'] : '',
                        'radius_map' => !empty($flashKeyword['radius']) ? $flashKeyword['radius'] : ''
                    ]) ?>

                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row mb-20">
                        <div class="col-md-12 col-sm-12 col-xs-12">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="view">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#photo" aria-controls="photo" role="tab" data-toggle="tab"><i class="aicon aicon-camera"></i> Suasana</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#menu" aria-controls="menu" role="tab" data-toggle="tab"><i class="aicon aicon-icon-budicon"></i> Menu</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content box bg-white">
                                            <div role="tabpanel" class="tab-pane fade in active" id="photo">
                                                <div class="row">
                                                    <div class="col-sm-10 col-sm-offset-1">

                                                        <?php
                                                        $images = [];
                                                        foreach ($modelBusiness['businessImages'] as $dataBusinessImage) {

                                                            $images[] = [
                                                                'title' => '',
                                                                'href' => Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $dataBusinessImage['image'],
                                                                'type' => 'image/jpeg',
                                                                'poster' => Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $dataBusinessImage['image'],
                                                            ];
                                                        }

                                                        echo dosamigos\gallery\Carousel::widget([
                                                            'items' => $images,
                                                            'json' => true,
                                                            'templateOptions' => ['id'=>'gallery_business'],
                                                            'clientOptions' => ['container'=>'#gallery_business'],
                                                            'options' => ['id'=>'gallery_business'],
                                                        ]); ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade in active" id="menu">
                                                <div class="row">
                                                    <div class="col-sm-10 col-sm-offset-1">

                                                        <?php
                                                        $imagesMenu = [];
                                                        if (!empty($modelBusiness['businessMenus'])):
                                                            foreach ($modelBusiness['businessMenus'] as $dataBusinessMenu) {

                                                                $imagesMenu[] = [
                                                                    'title' => '',
                                                                    'href' => Yii::getAlias('@uploadsUrl') . '/img/business_menu/' . $dataBusinessMenu['image'],
                                                                    'type' => 'image/jpeg',
                                                                    'poster' => Yii::getAlias('@uploadsUrl') . '/img/business_menu/' . $dataBusinessMenu['image'],
                                                                ];
                                                            }

                                                            echo dosamigos\gallery\Carousel::widget([
                                                                'items' => $imagesMenu,
                                                                'json' => true,
                                                                'templateOptions' => ['id'=>'gallery_menu'],
                                                                'clientOptions' => ['container'=>'#gallery_menu'],
                                                                'options' => ['id'=>'gallery_menu'],
                                                            ]);

                                                        else: ?>

                                                            <div class="titan-caption">
                                                                <div class="caption-content">
                                                                    <div class="font-alt titan-title-size-2">Saat ini menu belum tersedia </div>
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
                            </div>

                            <div class="row mt-20">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="box bg-white">
                                        <div class="box-title">
                                            <div class="row">
                                                <div class="col-sm-7 col-tab-7 col-xs-12">
                                                    <h4 class="font-alt mb-0"><?= $modelBusiness['name']; ?></h4>
                                                </div>

                                                <div class="visible-xs col-xs-12 clearfix"></div>

                                                <div class="col-sm-5 col-tab-5 col-xs-12">
                                                    <h5 class="mb-0">

                                                        <?php
                                                        $categories = '';
                                                        foreach ($modelBusiness['businessCategories'] as $dataBusinessCategory) {

                                                            $categories .= $dataBusinessCategory['category']['name'] . ' / ';

                                                        } ?>

                                                        <strong class="pull-right visible-lg visible-md visible-sm visible-tab m-0"><?= trim($categories, ' / ') ?></strong>
                                                        <h6 class="visible-xs mt-10"><?= trim($categories, ' / ') ?></h6>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="divider-w">

                                        <div class="box-content">
                                            <div class="row mt-10">
                                                <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 pull-right mb-10">
                                                    <div class="business-rating">

                                                        <?= $this->render('@frontend/views/data/business_rating.php', [
                                                            'modelBusinessDetail' => $modelBusiness['businessDetail'],
                                                            'modelBusinessDetailVote' => $modelBusiness['businessDetailVotes'],
                                                            'modelRatingComponent' => $modelRatingComponent,
                                                        ]) ?>

                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-7 col-sm-7 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="widget">
                                                                <ul class="icon-list">
                                                                    <li class="visible-lg visible-md visible-sm visible-tab">
                                                                        <i class="aicon aicon-home"></i>

                                                                        <?php
                                                                        echo AddressType::widget([
                                                                            'addressType' => $modelBusiness['businessLocation']['address_type'],
                                                                            'address' => $modelBusiness['businessLocation']['address']
                                                                        ]);

                                                                        echo Html::a(Yii::t('app', 'See Map'), '', ['id' => 'see-map-shortcut', 'class' => 'font-12 ']); ?>

                                                                    </li>
                                                                    <li class="visible-xs">
                                                                        <i class="aicon aicon-home"></i>

                                                                        <?php
                                                                        echo AddressType::widget([
                                                                            'addressType' => $modelBusiness['businessLocation']['address_type'],
                                                                            'address' => $modelBusiness['businessLocation']['address']
                                                                        ]);

                                                                        echo Html::a(Yii::t('app', 'See Map'), '', ['id' => 'see-map-shortcut-xs', 'class' => 'font-12 ']); ?>

                                                                    </li>
                                                                    <li>
                                                                        <i class="aicon aicon-rupiah"></i>

                                                                        <?php
                                                                        if (!empty($modelBusiness['businessDetail']['price_min']) && !empty($modelBusiness['businessDetail']['price_max'])) {

                                                                            echo Yii::$app->formatter->asShortCurrency($modelBusiness['businessDetail']['price_min']) . ' - ' . Yii::$app->formatter->asShortCurrency($modelBusiness['businessDetail']['price_max']);
                                                                        } else if (empty($modelBusiness['businessDetail']['price_min']) && !empty($modelBusiness['businessDetail']['price_max'])) {

                                                                            echo '0 - ' . Yii::$app->formatter->asShortCurrency($modelBusiness['businessDetail']['price_max']);
                                                                        } else if (empty($modelBusiness['businessDetail']['price_max']) && !empty($modelBusiness['businessDetail']['price_min'])) {

                                                                            echo Yii::$app->formatter->asShortCurrency($modelBusiness['businessDetail']['price_min']) . ' - 0';
                                                                        } else {

                                                                            echo '-';
                                                                        } ?>

                                                                    </li>
                                                                    <li><i class="aicon aicon-icon-phone-fill"></i> <?= !empty($modelBusiness['phone1']) ? $modelBusiness['phone1'] : '-' ?></li>

                                                                    <li class="icon-list-parent">
                                                                        <i class="aicon aicon-clock"></i>

                                                                        <?php
                                                                        if (!empty($modelBusiness['businessHours'])): ?>

                                                                            Jam operasional:
                                                                            <ul class="icon-list">

                                                                            <?php
                                                                            $days = ['Monday', 'Tuesday', 'Wednesday',
                                                                                    'Thursday', 'Friday', 'Saturday', 'Sunday'];

                                                                                foreach ($modelBusiness['businessHours'] as $dataBusinessHour):

                                                                                    $is24Hour = (($dataBusinessHour['open_at'] == '00:00:00') && ($dataBusinessHour['close_at'] == '24:00:00')) ? true : false; ?>

                                                                                    <li><?= ($is24Hour) ? Yii::t('app', $days[(intval($dataBusinessHour['day']) - 1)]) . ': ' . Yii::t('app','24 Hours') : Yii::t('app', $days[(intval($dataBusinessHour['day']) - 1)]) . ': ' . Yii::$app->formatter->asTime($dataBusinessHour['open_at']) . ' - ' . Yii::$app->formatter->asTime($dataBusinessHour['close_at']) ?></li>

                                                                                <?php
                                                                                endforeach; ?>

                                                                            </ul>

                                                                        <?php
                                                                        else: ?>

                                                                            -

                                                                        <?php
                                                                        endif;?>

                                                                    </li>
                                                                    <li class="tag">

                                                                        <?php
                                                                        foreach ($modelBusiness['businessProductCategories'] as $dataBusinessProductCategory): ?>

                                                                            <strong class="text-red">#</strong><?= $dataBusinessProductCategory['productCategory']['name']; ?>

                                                                        <?php
                                                                        endforeach; ?>

                                                                    </li>
                                                                    <li class="tag">

                                                                        <?php
                                                                        foreach ($modelBusiness['businessFacilities'] as $dataBusinessFacility): ?>

                                                                            <strong class="text-blue">#</strong><?= $dataBusinessFacility['facility']['name']; ?>

                                                                        <?php
                                                                        endforeach; ?>

                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-10 mb-10 visible-lg visible-md visible-sm visible-tab">
                                                <div class="col-lg-4 col-md-5 col-sm-5 col-tab-6 widget pull-right">
                                                    <ul class="link-icon list-inline text-center">
                                                        <li>
                                                            <a href="" id="write-review-shortcut">
                                                                <ul class="text-center">
                                                                    <li><i class="aicon aicon-document-edit aicon-1-2x"></i></li>
                                                                    <li>Review</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="" id="post-photo-shortcut">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-camera aicon-1-2x"></i>
                                                                    <li>Photo</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="" id="report-business-trigger">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-warning aicon-1-2x"></i>
                                                                    <li>Report</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-lg-8 col-md-7 col-sm-7 col-tab-6 widget">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <a href="#" class="message-feature">
                                                                <ul class="text-center">
                                                                    <li><i class="aicon aicon-icon-envelope aicon-1-2x"></i></li>
                                                                    <li>Message</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="booking-feature">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-inspection-checklist aicon-1-2x"></i>
                                                                    <li>Booking</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="row mt-10 mb-10 visible-xs">
                                                <div class="col-xs-12 widget pull-right">
                                                    <ul class="link-icon list-inline text-center">
                                                        <li>
                                                            <a href="" id="write-review-shortcut-xs">
                                                                <ul class="text-center">
                                                                    <li><i class="aicon aicon-document-edit aicon-1-2x"></i></li>
                                                                    <li>Review</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="" id="post-photo-shortcut-xs">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-camera aicon-1-2x"></i>
                                                                    <li>Photo</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="" id="report-business-trigger-xs">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-warning aicon-1-2x"></i>
                                                                    <li>Report</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xs-12 widget">
                                                    <ul class="link-icon list-inline text-center">
                                                        <li>
                                                            <a href="#" class="message-feature">
                                                                <ul class="text-center">
                                                                    <li><i class="aicon aicon-icon-envelope aicon-1-2x"></i></li>
                                                                    <li>Message</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="booking-feature">
                                                                <ul class="text-center">
                                                                    <i class="aicon aicon-inspection-checklist aicon-1-2x"></i>
                                                                    <li>Booking</li>
                                                                </ul>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="divider-w">

                                        <div class="box-footer">

                                            <?= Html::hiddenInput('business_id', $modelBusiness['id'], ['class' => 'business-id']) ?>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">

                                                    <?php
                                                    $selectedVisit = '';
                                                    $selectedLove = '';

                                                    if (!empty($modelBusiness['userVisits'][0])) {
                                                        $selectedVisit = 'selected';
                                                    }

                                                    if (!empty($modelBusiness['userLoves'][0])) {
                                                        $selectedLove = 'selected';
                                                    }?>

                                                    <ul class="list-inline mt-0 mb-0 visible-lg visible-md visible-sm visible-tab">
                                                        <li>
                                                            <div class="btn-group" role="group">

                                                                <?= Html::a('<i class="aicon aicon-icon-been-there"></i> Been Here', '', [
                                                                    'class' => 'btn btn-default btn-standard btn-round-4 been-here ' . $selectedVisit . '',
                                                                ]) ?>

                                                                <?= Html::a(!empty($modelBusiness['businessDetail']['visit_value']) ? $modelBusiness['businessDetail']['visit_value'] : '0', '', [
                                                                    'class' => 'btn btn-default btn-standard btn-round-4 been-here ' . $selectedVisit . ' count',
                                                                ]) ?>

                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="btn-group" role="group">

                                                                <?= Html::a('<i class="fa fa-heart"></i> Loves', '', [
                                                                    'class' => 'btn btn-default btn-standard btn-round-4 love-place ' . $selectedLove . '',
                                                                ]) ?>

                                                                <?= Html::a(!empty($modelBusiness['businessDetail']['love_value']) ? $modelBusiness['businessDetail']['love_value'] : '0', '', [
                                                                    'class' => 'btn btn-default btn-standard btn-round-4 love-place ' . $selectedLove . ' count',
                                                                ]) ?>

                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="btn-group" role="group">

                                                                <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'btn btn-default btn-standard btn-round-4 share-feature']) ?>

                                                            </div>
                                                        </li>
                                                    </ul>

                                                    <ul class="list-inline list-default mt-0 mb-0 visible-xs">
                                                        <li>

                                                            <?= Html::a(!empty($modelBusiness['businessDetail']['visit_value']) ? '<i class="aicon aicon-icon-been-there"></i> <span class="been-here count">' . $modelBusiness['businessDetail']['visit_value'] . '</span> Been Here' : '<i class="aicon aicon-icon-been-there"></i> 0 Been Here', null, ['class' => 'been-here ' . $selectedVisit . '']); ?>

                                                        </li>
                                                        <li>

                                                            <?= Html::a(!empty($modelBusiness['businessDetail']['love_value']) ? '<i class="fa fa-heart"></i> <span class="love-place count">' . $modelBusiness['businessDetail']['love_value'] . '</span> Loves' : '<i class="fa fa-heart"></i> 0 Loves', null, ['class' => 'love-place ' . $selectedLove . '']); ?>

                                                        </li>
                                                        <li>

                                                            <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-feature']); ?>

                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <?php
                            if (!empty($modelBusiness['businessPromos'])): ?>

                                <div id="special" class="row mt-10">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="box bg-white">
                                            <div class="box-title">
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <h4 class="font-alt m-0">Spesial &amp; Diskon !!</h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="divider-w">

                                            <div class="box-content">
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">

                                                        <?php
                                                        foreach ($modelBusiness['businessPromos'] as $dataBusinessPromo): ?>

                                                            <div class="row mb-10">
                                                                <div class="col-sm-4 col-tab-12 col-xs-12">
                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['page/detail-promo', 'id' => $dataBusinessPromo['id']]); ?>">

                                                                        <?php
                                                                        if (!empty($dataBusinessPromo['image']) && file_exists(Yii::getAlias('@uploads') . '/img/business_promo/' . $dataBusinessPromo['image'])){

                                                                            echo Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/business_promo/', $dataBusinessPromo['image'], 490, 276), ['class' => 'img-responsive img-component']);

                                                                        } else {

                                                                            echo Html::img(Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg', ['class' => 'img-responsive img-component']);

                                                                        } ?>

                                                                    </a>
                                                                </div>
                                                                <div class="col-sm-8 col-tab-12 col-xs-12">
                                                                    <h3 class="mt-0 mb-0">

                                                                        <?= Html::a($dataBusinessPromo['title'], ['page/detail-promo', 'id' => $dataBusinessPromo['id']]) ?>

                                                                    </h3>
                                                                    <p class="description mb-10">

                                                                        <?= $dataBusinessPromo['short_description'] ?>

                                                                    </p>
                                                                    <h4>

                                                                        <?= 'Berlaku ' . Yii::$app->formatter->asDate($dataBusinessPromo['date_start'], 'medium') . ' s/d ' . Yii::$app->formatter->asDate($dataBusinessPromo['date_end'], 'medium') ?>

                                                                    </h4>
                                                                    <p>

                                                                        <?= Html::a('<span class="text-red"> View detail <i class="fa fa-angle-double-right"></i></span>', ['page/detail-promo', 'id' => $dataBusinessPromo['id']]) ?>

                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <hr class="divider-w mb-10">

                                                        <?php
                                                        endforeach; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            endif; ?>

                            <div class="row mt-20">
                                <div class="col-sm-12 col-xs-12">

                                    <div class="view">
                                        <ul class="nav nav-tabs widget mb-10" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#view-review" aria-controls="view-review" role="tab" data-toggle="tab">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="aicon aicon-document-edit aicon-1-5x"></i>
                                                                <li>Review</li>
                                                                <span class="badge total-review"></span>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#view-about" aria-controls="view-about" role="tab" data-toggle="tab">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="aicon aicon-icon-restaurant aicon-1-5x"></i>
                                                                <li>About</li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                            </li>
                                            <li role="presentation" class="visible-lg visible-md visible-sm visible-tab">
                                                <a href="#view-photo" aria-controls="view-photo" role="tab" data-toggle="tab">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="aicon aicon-camera aicon-1-5x"></i>
                                                                <li>Photo</li>
                                                                <span class="badge total-photo"></span>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                            </li>
                                            <li role="presentation" class="visible-lg visible-md visible-sm visible-tab">
                                                <a href="#view-menu" aria-controls="view-menu" role="tab" data-toggle="tab">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="aicon aicon-icon-budicon aicon-1-5x"></i>
                                                                <li>Menu</li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                            </li>
                                            <li role="presentation" class="visible-lg visible-md visible-sm visible-tab">
                                                <a href="#view-map" aria-controls="view-map" role="tab" data-toggle="tab">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="aicon aicon-icon-thin-location-line aicon-1-5x"></i>
                                                                <li>Map</li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                            </li>
                                            <li role="presentation" class="dropdown visible-xs">
                                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                    <ul class="link-icon list-inline">
                                                        <li>
                                                            <ul class="text-center">
                                                                <i class="fa fa-ellipsis-h aicon-1-5x"></i>
                                                                <li>More <span class="caret"></span></li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li role="presentation">
                                                        <a href="#view-photo" aria-controls="view-photo-xs" role="tab" data-toggle="tab"><h6><i class="aicon aicon-camera"></i> Photo (<span class="total-photo"></span>)</h6></a>
                                                    </li>
                                                    <li role="presentation">
                                                        <a href="#view-menu" aria-controls="view-menu" role="tab" data-toggle="tab"><h6><i class="aicon aicon-icon-budicon"></i> Menu</h6></a>
                                                    </li>
                                                    <li role="presentation">
                                                        <a href="#view-map" aria-controls="view-map-xs" role="tab" data-toggle="tab"><h6><i class="aicon aicon-icon-thin-location-line"></i> Map</h6></a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>

                                        <div class="tab-content">

                                            <div role="tabpanel" class="tab-pane fade in active p-0" id="view-review">

                                                <?= $this->render('detail/_review.php', [
                                                    'modelBusiness' => $modelBusiness,
                                                    'modelUserPostMain' => $modelUserPostMain,
                                                    'dataUserVoteReview' => $dataUserVoteReview,
                                                    'modelPost' => $modelPost,
                                                    'modelRatingComponent' => $modelRatingComponent,
                                                ]) ?>

                                            </div>

                                            <div role="tabpanel" class="tab-pane fade p-0" id="view-about">

                                                <?= $this->render('detail/_about.php', [
                                                    'businessAbout' => $modelBusiness['about'],
                                                    'businessName' => $modelBusiness['name'],
                                                ]) ?>

                                            </div>

                                            <div role="tabpanel" class="tab-pane fade p-0" id="view-photo">

                                                <?= $this->render('detail/_photo.php', [
                                                    'modelBusiness' => $modelBusiness,
                                                    'modelPostPhoto' => $modelPostPhoto,
                                                ]) ?>

                                            </div>

                                            <div role="tabpanel" class="tab-pane fade p-0" id="view-menu">

                                                <?= $this->render('detail/_menu.php', [
                                                    'modelBusinessMenu' => $modelBusiness['businessMenus'],
                                                ]) ?>

                                            </div>

                                            <div role="tabpanel" class="tab-pane fade p-0" id="view-map">

                                                <?= $this->render('detail/_map.php', [
                                                    'coordinate' => explode(',', $modelBusiness['businessLocation']['coordinate']),
                                                ]) ?>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

</div>

<?php
$this->params['businessId'] = $modelBusiness['id'];
$this->params['modelUserReport'] = $modelUserReport;
$this->params['beforeEndBody'][] = function() {

    Modal::begin([
        'header' => 'Coming Soon',
        'id' => 'modal-coming-soon',
        'size' => Modal::SIZE_SMALL,
    ]);

    echo 'Fitur ini akan segera hadir';

    Modal::end();

    Modal::begin([
        'header' => '<i class="aicon aicon-warning"></i> Report',
        'id' => 'modal-report',
    ]);

    echo '<div class="overlay" style="display: none;"></div>';
    echo '<div class="loading-img" style="display: none"></div>';

    $form = ActiveForm::begin([
        'id' => 'report-form',
        'action' => ['action/submit-report'],
        'fieldConfig' => [
            'template' => '{input}{error}'
        ],
    ]);

    echo Html::hiddenInput('business_id', $this->params['businessId']);

    echo '<label>Bisnis ini:</label>';
    echo $form->field($this->params['modelUserReport'], 'report_status')
            ->radioList([
                'Closed' => 'Tutup',
                'Moved'=> 'Pindah',
                'Duplicate' => 'Duplikat / fiktif',
                'Inaccurate' => 'Informasi tidak akurat',
            ],
            [
                'separator' => '<br>',
                'itemOptions' => [
                    'class' => 'report-subject icheck',
                ],
            ]);

    echo '<label>Keterangan:</label>';
    echo $form->field($this->params['modelUserReport'], 'text')->textArea([
        'rows' => 3,
        'placeholder' => 'Ceritakan mengenai situasi atau keluhan anda.'
    ]);

    echo '
        <div class="row">
            <div class="col-sm-12 col-md-12 text-center">
                ' . Html::submitButton('Submit', ['class' => 'btn btn-round btn-d']) . '
                ' . Html::a('Close', null, ['class' => 'btn btn-round btn-default btn-close-modal']) . '
            </div>
        </div>';

    ActiveForm::end();

    Modal::end();

    Modal::begin([
        'header' => 'Konfirmasi',
        'id' => 'modal-confirmation',
        'size' => Modal::SIZE_SMALL,
        'footer' => '<button class="btn btn-default" data-dismiss="modal" type="button">Batal</button>
                    <button id="btn-delete" class="btn btn-danger" type="button">Hapus</button>',
    ]);

    echo 'Anda yakin akan menghapus foto ini?';

    Modal::end();
};

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    window.fbAsyncInit = function() {
        FB.init({
            appId            : "1567716833343397",
            autoLogAppEvents : true,
            xfbml            : true,
            version          : "v3.1"
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, "script", "facebook-jssdk"));

    $("#see-map-shortcut").on("click", function(event) {

        if (!$("a[aria-controls=\"view-map\"]").parent().hasClass("active")) {

            $("a[aria-controls=\"view-map\"]").tab("show");

            $("a[aria-controls=\"view-map\"]").on("shown.bs.tab", function (e) {

                $("html, body").animate({ scrollTop: $("#title-map").offset().top }, "slow");
                $(this).off("shown.bs.tab");
            });
        } else {
            $("html, body").animate({ scrollTop: $("#title-map").offset().top }, "slow");
        }

        return false;
    });

    $("#see-map-shortcut-xs").on("click", function(event) {

        if (!$("a[aria-controls=\"view-map-xs\"]").parent().hasClass("active")) {

            $("a[aria-controls=\"view-map-xs\"]").tab("show");

            $("a[aria-controls=\"view-map-xs\"]").on("shown.bs.tab", function (e) {

                $("html, body").animate({ scrollTop: $("#title-map").offset().top }, "slow");
                $(this).off("shown.bs.tab");
            });
        } else {
            $("html, body").animate({ scrollTop: $("#title-map").offset().top }, "slow");
        }

        return false;
    });

    $(".love-place").on("click", function() {

        $.ajax({
            url: "'. Yii::$app->urlManager->createUrl('action/submit-user-love').'",
            type: "POST",
            data: {
                "business_id": $(".business-id").val()
            },
            success: function(response) {

                if(response.status == "sukses") {

                    var count = parseInt($(".love-place.count").html());

                    if(response.is_active) {

                        $(".love-place").addClass("selected");
                        $(".love-place.count").html(count + 1);
                    } else {

                        $(".love-place").removeClass("selected");
                        $(".love-place.count").html(count - 1);
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

    $(".been-here").on("click", function() {

        $.ajax({
            url: "'. Yii::$app->urlManager->createUrl('action/submit-user-visit').'",
            type: "POST",
            data: {
                "business_id": $(".business-id").val()
            },
            success: function(response) {

                if(response.status == "sukses") {
                    var count = parseInt($(".been-here.count").html());

                    if(response.is_active) {

                        $(".been-here").addClass("selected");
                        $(".been-here.count").html(count + 1);
                    } else {

                        $(".been-here").removeClass("selected");
                        $(".been-here.count").html(count - 1);
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

    $(".message-feature").on("click", function() {

        $("#modal-coming-soon").modal("show");

        return false;
    });

    $(".booking-feature").on("click", function() {

        $("#modal-coming-soon").modal("show");

        return false;
    });

    $(".share-feature").on("click", function() {

        url = window.location.href;

        FB.ui({
            method: "share",
            href: url,
        }, function(response){});

        return false;
    });

    $("#report-business-trigger").on("click", function() {

        $("#modal-report").modal("show");

        return false;
    });

    $("#report-business-trigger-xs").on("click", function() {

        $("#modal-report").modal("show");

        return false;
    });

    $(".btn-close-modal").on("click", function() {

        $("#modal-report").modal("hide");

        return false;
    });

    $("form#report-form").on("beforeSubmit", function(event) {

        var thisObj = $(this);

        if(thisObj.find(".has-error").length)  {
            return false;
        }

        var formData = new FormData(this);

        var endUrl = thisObj.attr("action");

        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            type: "POST",
            data: formData,
            url: thisObj.attr("action"),
            beforeSend: function(xhr) {
                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-img").show();
            },
            success: function(response) {

                if (response.status == "sukses") {

                    $("#modal-report").modal("hide");

                    $("#modal-report").find("#userreport-text").val("");
                    $("#modal-report").find(".form-group").removeClass("has-success");

                    messageResponse(response.icon, response.title, response.message, response.type);
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            }
        });

        return false;
    });

    $("#modal-report").on("hidden.bs.modal", function() {

        $(this).find("#userreport-report_status").find("input.report-subject").iCheck("uncheck");
        $(this).find(".form-group").removeClass("has-error");
        $(this).find(".form-group").removeClass("has-success");
        $(this).find(".form-group").find(".help-block").html("");
    });

    $("#menu").removeClass("in active");
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>

