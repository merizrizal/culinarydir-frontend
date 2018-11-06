<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelBusinessPromo core\models\BusinessPromo */

kartik\popover\PopoverXAsset::register($this);

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
]);

$jspopover = ''; ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row mt-10">
            <div class="col-lg-6 col-md-12 col-tab-6 col-xs-12 mb-10">

                <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

            </div>
            <div class="col-lg-6 visible-lg text-right">

                <?= $linkPager; ?>

            </div>
            <div class="col-tab-6 visible-tab text-right">

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
            if (!empty($modelBusinessPromo)):

                $businessPromoDetail = [];

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
                            <div class="box box-small promo-<?= $dataBusinessPromo['business']['id']; ?>" role="button">

                                <div class="row">
                                    <div class="col-md-5 col-sm-12 col-tab-6 col-xs-12 col">

                                        <?php
                                        $image = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 347, 210);

                                        if (!empty($dataBusinessPromo['image'])) {

                                            $image = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/business_promo/', $dataBusinessPromo['image'], 490, 276);
                                        }

                                        echo Html::img($image); ?>

                                    </div>
                                    <div class="col-md-7 col-sm-12 col-tab-6 col-xs-12 col">
                                        <div class="short-desc">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col">
                                                    <h4 class="font-alt m-0"><?= $dataBusinessPromo['title'] ?></h4>
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
                                                            <li class="tag">

                                                                <?php
                                                                $businessProductCategoryLimit = 3;
                                                                $businessProductCategoryList = '';
                                                                $businessProductCategoryPopover = '';

                                                                foreach ($dataBusinessPromo['business']['businessProductCategories'] as $key => $dataBusinessProductCategory) {

                                                                    if ($key < $businessProductCategoryLimit) {

                                                                        $businessProductCategoryList .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
                                                                    } else {

                                                                        $businessProductCategoryPopover .= '<strong class="text-red">#</strong>' . $dataBusinessProductCategory['productCategory']['name'] . ' ';
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
                    $businessCoordinate = explode(',', $dataBusinessPromo['business']['businessLocation']['coordinate']);
                    $businessLatitude = $businessCoordinate[0];
                    $businessLongitude = $businessCoordinate[1];

                    $key = $dataBusinessPromo['business']['businessLocation']['coordinate'];

                    $businessPromoDetail[$key][] = [
                        'businessId' => $dataBusinessPromo['business']['id'],
                        'businessPromoImage' => $image,
                        'businessPromoTitle' => $dataBusinessPromo['title'],
                        'businessName' => $dataBusinessPromo['business']['name'],
                        'businessLatitude' => $businessLatitude,
                        'businessLongitude' => $businessLongitude,
                        'businessPromoUrl' => Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusinessPromo['business']['id'], '#' => 'special'])
                    ];

                endforeach;

                $mapObject = json_encode($businessPromoDetail); ?>

                <textarea id="map-object" style="display: none;"><?= $mapObject ?></textarea>

            <?php
            endif; ?>

        </div>
    </div>
</div>

<?php
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC84sFxZL4KCPIFl8ezsta45Rm8WPRIM7Y', ['depends' => 'yii\web\YiiAsset']);

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
                var businessPromoDetail = jQuery.parseJSON(mapObject);
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

                $.each(businessPromoDetail, function(keyCoordinate, businessPromoData) {

                    var coordinate = keyCoordinate.split(",");
                    businessLatLng[keyCoordinate] = new google.maps.LatLng(coordinate[0], coordinate[1]);

                    infoWindowContent[keyCoordinate] = "<div class=\"infowindow mt-10\">";

                    var businessPromoMarker = new google.maps.Marker({
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

                    markers[keyCoordinate] = businessPromoMarker;

                    $.each(businessPromoData, function(key, value) {

                        $(".promo-" + value.businessId).on("click", callbackMarkerListener(keyCoordinate));

                        infoWindowContent[keyCoordinate] +=
                            "<div class=\"row\">" +
                                "<div class=\"col-sm-5 hidden-xs\">" +
                                    "<img src=\"" + value.businessPromoImage + "\" width=\"100%\">" +
                                "</div>" +
                                "<div class=\"col-sm-7 col-xs-12\">" +
                                    "<div class=\"short-desc\">" +
                                        "<div class=\"row\">" +
                                            "<div class=\"col-sm-12 col-xs-12\">" +
                                                "<h5 class=\"font-alt m-0\">" + value.businessPromoTitle + "</h5>" +
                                            "</div>" +
                                        "</div>" +
                                        "<div class=\"row\">" +
                                            "<div class=\"col-sm-12 col-xs-12\">" +
                                                value.businessName +
                                                "<br>" +
                                                "<a class=\"text-main pull-right\" href=\"" + value.businessPromoUrl + "\">' . Yii::t('app', 'View Details') . ' <i class=\"fa fa-angle-double-right\"></i></a>" +
                                            "</div>" +
                                        "</div>" +
                                    "</div>" +
                                "</div>" +
                            "</div>" +
                            "<hr class=\"divider-w mt-10 mb-10\">";
                    });

                    infoWindowContent[keyCoordinate] += "</div>";

                    google.maps.event.addListener(businessPromoMarker, "click", callbackMarkerListener(keyCoordinate));

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

    $(".popover-tag").on("click", function() {

        return false;
    });

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
';

$this->registerJs($jscript . $jspopover);

Pjax::end(); ?>