<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
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

common\assets\OwlCarouselAsset::register($this);

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-result-map a',
    'options' => ['id' => 'pjax-result-map-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 3,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-result-map', 'class' => 'pagination'],
]); ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row mt-10">
            <div class="col-lg-6 col-md-12 col-tab-6 col-xs-12 mb-10">

                <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

            </div>
            <div class="col-lg-6 col-tab-6 visible-lg visible-tab text-right">

                <?= $linkPager; ?>

            </div>
            <div class="col-md-12 col-xs-12 visible-md visible-sm visible-xs">

                <?= $linkPager; ?>

            </div>
        </div>
    </div>
</div>

<div class="container-map-detail">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 box-place">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none;"></div>

            <?php
            if (!empty($modelBusiness)):

                $businessDetail = [];

                foreach ($modelBusiness as $dataBusiness): ?>

                    <div class="row mb-10">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="box box-small place-<?= $dataBusiness['id']; ?>" role="button">
                                <div class="row">
                                    <div class="col-md-5 col-sm-7 col-tab-7 col-xs-7 col">
                                        <div class="result-map-image owl-carousel owl-theme">

                                            <?php
                                            $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 429, 241);
                                            
                                            if (!empty($dataBusiness['businessImages'])) {

                                                $img = Yii::$app->params['endPointLoadImage'] . 'registry-business?image=' . $dataBusiness['businessImages'][0]['image'] . '&w=429&h=241';
                                            }
                                            
                                            echo Html::img($img); ?>
                                            
                                        </div>
                                    </div>
									
									<?php 
									$voteValue = !empty($dataBusiness['businessDetail']['vote_value']) ? $dataBusiness['businessDetail']['vote_value'] : 0;
									$voters = !empty($dataBusiness['businessDetail']['voters']) ? $dataBusiness['businessDetail']['voters'] : 0;
									
									$layoutRatings = '
                                        <div class="rating rating-top">
                                            <h2 class="mt-10 mb-0"><span class="label label-success pt-10">' . number_format($voteValue, 1) . '</span></h2>' .
                                            Yii::t('app', '{value, plural, =0{# Vote} =1{# Vote} other{# Votes}}', ['value' => $voters]) . '
                                        </div>
                                    '; ?>
									
                                    <div class="col-tab-5 col-sm-5 col visible-tab visible-sm text-center">
                                        <?= $layoutRatings ?>
                                    </div>

                                    <div class="col-xs-5 col visible-xs text-center">
                                        <?= $layoutRatings ?> 
                                    </div>

                                    <div class="col-md-7 col-sm-12 col-xs-12 col">
                                        <div class="short-desc">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="m-0"><?= $dataBusiness['name'] ?></h4>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-9 col-sm-12 col-xs-12 col">
                                                    <h4 class="m-0">

                                                        <?php
                                                        $businessCategory = '';

                                                        foreach ($dataBusiness['businessCategories'] as $dataBusinessCategories) {

                                                            $businessCategory .= $dataBusinessCategories['category']['name'] . ' / ';
                                                        }

                                                        $businessCategory = trim($businessCategory, ' / '); ?>

                                                        <small class="mt-10"><?= $businessCategory ?></small>

                                                    </h4>
                                                    <div class="widget">
                                                        <ul class="icon-list">
                                                            <li>
                                                                <i class="aicon aicon-home"></i>
                                                                <?= AddressType::widget(['businessLocation' => $dataBusiness['businessLocation']]) ?>
                                                            </li>
                                                            <li>
                                                                <i class="aicon aicon-rupiah"></i>

                                                                <?php
                                                                $businessPrice = '-';

                                                                if (!empty($dataBusiness['businessDetail']['price_min']) && !empty($dataBusiness['businessDetail']['price_max'])) {

                                                                    $businessPrice = Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']) . ' - ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);
                                                                } else if (empty($dataBusiness['businessDetail']['price_min']) && !empty($dataBusiness['businessDetail']['price_max'])) {

                                                                    $businessPrice = Yii::t('app', 'Under') . ' ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);
                                                                } else if (empty($dataBusiness['businessDetail']['price_max']) && !empty($dataBusiness['businessDetail']['price_min'])) {

                                                                    $businessPrice = Yii::t('app', 'Above') . ' ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']);
                                                                }

                                                                echo $businessPrice ?>

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col visible-lg visible-md text-center">
                                                    <div class="rating pull-right">
                                                        <h3 class="mt-0 mb-0"><span class="label label-success pt-10"><?= number_format($voteValue, 1); ?></span></h3>
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
                    $businessCoordinate = explode(',', $dataBusiness['businessLocation']['coordinate']);
                    $businessLatitude = $businessCoordinate[0];
                    $businessLongitude = $businessCoordinate[1];

                    $key = $dataBusiness['businessLocation']['coordinate'];

                    $businessDetail[$key][] = [
                        'businessId' => $dataBusiness['id'],
                        'businessImage' => $img,
                        'businessName' => $dataBusiness['name'],
                        'businessCategory' => $businessCategory,
                        'businessAddress' => AddressType::widget(['businessLocation' => $dataBusiness['businessLocation']]),
                        'businessPrice' => $businessPrice,
                        'businessLatitude' => $businessLatitude,
                        'businessLongitude' => $businessLongitude,
                        'businessUrl' => Yii::$app->urlManager->createUrl([
                            'page/detail',
                            'city' => Inflector::slug($dataBusiness['businessLocation']['city']['name']),
                            'uniqueName' => $dataBusiness['unique_name']
                        ])
                    ];

                endforeach;

                $mapObject = Json::encode($businessDetail); ?>

                <textarea id="map-object" style="display: none"><?= $mapObject ?></textarea>

            <?php
            endif; ?>

        </div>
    </div>
</div>

<?php
$jscript = '
    function initMap() {

        var mapResult;
        var mapOptions;
        var mapResultDefaultLatLng = {lat: -6.9175, lng: 107.6191};

        var mapResultContainer = document.getElementById("maps");
        var mapObject = $("#map-object").val();

        mapResultContainer.style.width = "100%";
        mapResultContainer.style.height = "515px";

        if (mapResultContainer) {

            mapOptions = {
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                },
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] }, { "featureType": "poi.park", "elementType": "labels.text", "stylers": [ { "visibility": "off" } ] } ],
            };

            mapResult = new google.maps.Map(mapResultContainer, mapOptions);

            if (mapObject) {

                var mapInfoWindow = new google.maps.InfoWindow();
                var businessDetail = jQuery.parseJSON(mapObject);
                var markerBounds = new google.maps.LatLngBounds();
                var markers = {};
                var infoWindowContent = {};
                var businessLatLng = {};

                var callbackMarkerListener = function(keyCoordinate) {

                    return function () {

                        mapInfoWindow.setOptions({
                            content: infoWindowContent[keyCoordinate],
                            maxWidth: 800,
                        });

                        mapInfoWindow.open(mapResult, markers[keyCoordinate]);
                    }
                };

                $.each(businessDetail, function(keyCoordinate, businessData) {

                    var coordinate = keyCoordinate.split(",");
                    businessLatLng[keyCoordinate] = new google.maps.LatLng(coordinate[0], coordinate[1]);

                    infoWindowContent[keyCoordinate] = "<div class=\"infowindow mt-10\">";

                    var businessMarker = new google.maps.Marker({
                        map: mapResult,
                        position: businessLatLng[keyCoordinate],
                        gestureHandling: "greedy",
                        icon: {
                            url: "' . Yii::$app->request->baseUrl . '/media/img/dot.png",
                            scaledSize: new google.maps.Size(28, 28),
                            origin: new google.maps.Point(0,0),
                            anchor: new google.maps.Point(10, 25)
                        }
                    });

                    markers[keyCoordinate] = businessMarker;

                    $.each(businessData, function(key, value) {

                        $(".place-" + value.businessId).on("click", callbackMarkerListener(keyCoordinate));

                        infoWindowContent[keyCoordinate] +=
                            "<div class=\"row\">" +
                                "<div class=\"col-sm-5 hidden-xs\">" +
                                    "<img src=\"" + value.businessImage + "\" width=\"100%\">" +
                                "</div>" +
                                "<div class=\"col-sm-7 col-xs-12\">" +
                                    "<div class=\"short-desc\">" +
                                        "<div class=\"row\">" +
                                            "<div class=\"col-sm-12 col-xs-12\">" +
                                                "<h5 class=\"m-0\">" + value.businessName + "</h5>" +
                                            "</div>" +
                                        "</div>" +
                                        "<div class=\"row\">" +
                                            "<div class=\"col-sm-12 col-xs-12\">" +
                                                value.businessCategory +
                                                "<div class=\"widget\">" +
                                                    "<ul class=\"icon-list\">" +
                                                        "<li>" +
                                                            "<i class=\"aicon aicon-home\"></i> " + value.businessAddress +
                                                        "</li>" +
                                                        "<li>" +
                                                            "<i class=\"aicon aicon-rupiah\"></i> " + value.businessPrice +
                                                        "</li>" +
                                                    "</ul>" +
                                                "</div>" +
                                                "<a class=\"text-main pull-right\" href=\"" + value.businessUrl + "\">' . Yii::t('app', 'View Details') . ' <i class=\"fa fa-angle-double-right\"></i></a>" +
                                            "</div>" +
                                        "</div>" +
                                    "</div>" +
                                "</div>" +
                            "</div>" +
                            "<hr class=\"divider-w mt-10 mb-10\">";
                    });

                    infoWindowContent[keyCoordinate] += "</div>";

                    google.maps.event.addListener(businessMarker, "click", callbackMarkerListener(keyCoordinate));

                    markerBounds.extend(businessLatLng[keyCoordinate]);
                });

                mapResult.fitBounds(markerBounds);
            } else {

                mapOptions = {
                    center: mapResultDefaultLatLng,
                    zoom: 14,
                    disableDefaultUI: true,
                    styles: [ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] }, { "featureType": "poi.park", "elementType": "labels.text", "stylers": [ { "visibility": "off" } ] } ],
                };

                mapResult = new google.maps.Map(mapResultContainer, mapOptions);
            }
        }
    };

    initMap();

    ratingColor($(".rating"), "span");

    $("#pjax-result-map-container").off("pjax:send");
    $("#pjax-result-map-container").on("pjax:send", function() {

        $(".box-place").children(".overlay").show();
        $(".box-place").children(".loading-img").show();
    });

    $("#pjax-result-map-container").off("pjax:complete");
    $("#pjax-result-map-container").on("pjax:complete", function() {

        $(".box-place").children(".overlay").hide();
        $(".box-place").children(".loading-img").hide();
    });

    $("#pjax-result-map-container").off("pjax:error");
    $("#pjax-result-map-container").on("pjax:error", function (event) {

        event.preventDefault();
    });

    $(".result-map-image").owlCarousel({

        lazyLoad: true,
        items: 1
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>