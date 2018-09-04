<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use frontend\components\AddressType;

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

                <?= 'Showing ' . $startItem . ' - ' . $endItem . ' of ' . $totalCount . ' results'; ?>

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
            <div class="loading-img" style="display: none"></div>

            <?php
            if (!empty($modelBusiness)):

                $businessDetail = [];

                foreach ($modelBusiness as $dataBusiness): ?>

                    <div class="row mb-10">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="box box-small place-<?= $dataBusiness['id']; ?>">

                                <div class="row">
                                    <div class="col-md-5 col-sm-6 col-xs-6 col" role="button">

                                        <?php
                                        $businessImage = [];

                                        if (count($dataBusiness['businessImages']) > 1) {

                                            $images = [];

                                            foreach ($dataBusiness['businessImages'] as $dataBusinessImage) {

                                                $href = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-490-276.jpg';

                                                if (!empty($dataBusinessImage['image'] && file_exists(Yii::getAlias('@uploads') . '/img/registry_business/' . $dataBusinessImage['image']))) {

                                                    $href = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataBusinessImage['image'], 490, 276);
                                                }

                                                $images[] = [
                                                    'title' => '',
                                                    'href' => $href,
                                                    'type' => 'image/jpeg',
                                                    'poster' => $href,
                                                ];

                                                $businessImage[] = $href;
                                            }

                                            echo dosamigos\gallery\Carousel::widget([
                                                'items' => $images,
                                                'json' => true,
                                                'templateOptions' => ['id' => 'blueimp-gallery-' . $dataBusiness['id']],
                                                'clientOptions' => ['container' => '#blueimp-gallery-' . $dataBusiness['id']],
                                                'options' => ['id' => 'blueimp-gallery-' . $dataBusiness['id']],
                                            ]);
                                        } else {

                                            foreach ($dataBusiness['businessImages'] as $dataBusinessImage) {

                                                $src = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-347-210.jpg';

                                                if (!empty($dataBusinessImage['image'] && file_exists(Yii::getAlias('@uploads') . '/img/registry_business/' . $dataBusinessImage['image']))) {

                                                    $src = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataBusinessImage['image'], 490, 276);

                                                }
                                                
                                                echo Html::img($src, ['class' => 'img-responsive img-component']);

                                                $businessImage[] = $src;
                                            }
                                        } ?>

                                    </div>

                                    <div class="col-tab-5 col-sm-5 col visible-tab visible-sm text-center">
                                        <div class="rating rating-top">
                                            <h2 class="mt-10 mb-0"><span class="label label-success"><?= (!empty($dataBusiness['businessDetail']['vote_value']) ? number_format((float)$dataBusiness['businessDetail']['vote_value'], 1, '.', '') : '0.0'); ?></span></h2>
                                            <?= (!empty($dataBusiness['businessDetail']['voters']) ? $dataBusiness['businessDetail']['voters'] : '0'); ?> votes
                                        </div>
                                    </div>

                                    <div class="col-xs-5 col visible-xs text-center">
                                        <div class="rating rating-top">
                                            <h2 class="mt-10 mb-0"><span class="label label-success"><?= (!empty($dataBusiness['businessDetail']['vote_value']) ? number_format((float)$dataBusiness['businessDetail']['vote_value'], 1, '.', '') : '0.0'); ?></span></h2>
                                            <?= (!empty($dataBusiness['businessDetail']['voters']) ? $dataBusiness['businessDetail']['voters'] : '0'); ?> votes
                                        </div>
                                    </div>

                                    <div class="col-md-7 col-sm-12 col-xs-12 col">
                                        <div class="short-desc">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 col" role="button">
                                                    <h4 class="font-alt m-0"><?= $dataBusiness['name'] ?></h4>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-9 col-sm-6 col-xs-12 col">
                                                    <h4 class="m-0">

                                                        <?php
                                                        $businessCategory = '';

                                                        foreach ($dataBusiness['businessCategories'] as $dataBusinessCategories) {

                                                            $businessCategory .= $dataBusinessCategories['category']['name'] . ' / ';

                                                        } ?>

                                                        <small class="mt-10"><?= trim($businessCategory, ' / ') ?></small>

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

                                                                    $businessPrice = Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']) . ' - ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);

                                                                } else if (empty($dataBusiness['businessDetail']['price_min']) && !empty($dataBusiness['businessDetail']['price_max'])) {

                                                                    $businessPrice = '0 - ' . Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_max']);

                                                                } else if (empty($dataBusiness['businessDetail']['price_max']) && !empty($dataBusiness['businessDetail']['price_min'])) {

                                                                    $businessPrice = Yii::$app->formatter->asShortCurrency($dataBusiness['businessDetail']['price_min']) . ' - 0';

                                                                } else {

                                                                    $businessPrice = '-';

                                                                }

                                                                echo $businessPrice ?>

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="visible-lg visible-md col-md-3 col text-center">
                                                    <div class="rating pull-right">
                                                        <h3 class="mt-0 mb-0"><span class="label label-success pt-10"><?= (!empty($dataBusiness['businessDetail']['vote_value']) ? number_format((float)$dataBusiness['businessDetail']['vote_value'], 1, '.', '') : '0.0'); ?></span></h3>

                                                        <?= (!empty($dataBusiness['businessDetail']['voters']) ? $dataBusiness['businessDetail']['voters'] : '0'); ?> votes

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
                    $businessDetail[$dataBusiness['id']][] = [
                        'businessId' => $dataBusiness['id'],
                        'businessImage' => $businessImage,
                        'businessName' => $dataBusiness['name'],
                        'businessCategory' => $businessCategory,
                        'businessAddress' => AddressType::widget([
                            'addressType' => $dataBusiness['businessLocation']['address_type'],
                            'address' => $dataBusiness['businessLocation']['address']
                        ]),
                        'businessPrice' => $businessPrice,
                        'businessLatitude' => $businessLatitude,
                        'businessLongitude' => $businessLongitude,
                        'businessUrl' => Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataBusiness['id']])
                    ];

                endforeach;

                $mapObject = json_encode($businessDetail); ?>

                <textarea id="map-object" style="display: none"><?= $mapObject ?></textarea>

            <?php
            endif; ?>

        </div>
    </div>
</div>

<?php

$csscript = '
    .widget .icon-list li a::before {
        content: none;
    }

    .detail .box {
        padding: 0;
    }

    .in-result .box {
        border-radius: 0px;
    }
';

$this->registerCss($csscript);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC84sFxZL4KCPIFl8ezsta45Rm8WPRIM7Y', ['depends' => 'yii\web\YiiAsset']);

frontend\components\RatingColor::widget();

$jscript = '
    ratingColor($(".rating"), "span");

    function trim(word, mask) {

        while (~mask.indexOf(word[0])) {

            word = word.slice(1);
        }

        while (~mask.indexOf(word[word.length - 1])) {

            word = word.slice(0, -1);
        }

        return word;
    }

    var initMap = function() {

        var mapInfoWindow;
        var mapResult;
        var mapOptions;
        var mapResultDefaultLatLng = {lat: -6.9175, lng: 107.6191};

        var mapResultContainer = document.getElementById("result-map");
        var mapObject = $("#map-object").val();

        mapResultContainer.style.width = "100%";
        mapResultContainer.style.height = "515px";

        if (mapResultContainer) {

            mapOptions = {
                center: mapResultDefaultLatLng,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] }, { "featureType": "poi.park", "elementType": "labels.text", "stylers": [ { "visibility": "off" } ] } ],
            };

            mapResult = new google.maps.Map(mapResultContainer, mapOptions);
            mapInfoWindow = new google.maps.InfoWindow();

            if(mapObject) {

                var mapInfoWindow = new google.maps.InfoWindow();
                var mapObject = mapObject.replace();
                var businessDetail = jQuery.parseJSON(mapObject);
                var markers = [];
                var markerIndex = 0;
                var markerBounds = new google.maps.LatLngBounds();

                $.each(businessDetail, function(businessId, businessData) {

                    var businessLatLng = new google.maps.LatLng(businessData[0].businessLatitude, businessData[0].businessLongitude);
                    var businessMarker;

                    var infoWindowContent = "<div class=\"infowindow mt-10\">";

                    $.each(businessData, function(key, value) {

                        businessMarker = new google.maps.Marker({
                            map: mapResult,
                            position: businessLatLng,
                            gestureHandling: "greedy",
                            title: value.businessName,
                            icon: {
                                url: "' . Yii::$app->request->baseUrl . '/media/img/dot.png",
                                scaledSize: new google.maps.Size(28, 28),
                                origin: new google.maps.Point(0,0),
                                anchor: new google.maps.Point(10, 25)
                            }
                        });

                        markers.push(businessMarker);

                        infoWindowContent += "<div class=\"col-sm-6 hidden-xs\">" +
                            "<img src=\"" + value.businessImage[0] + "\" width=\"100%\">" +
                            "</div>" +
                            "<div class=\"col-sm-6 col-xs-12\">" +
                            "<div class=\"short-desc\">" +
                            "<div class=\"row\">" +
                            "<div class=\"col-sm-12 col-xs-12\">" +
                            "<h4 class=\"font-alt m-0\">" + value.businessName + "</h4>" +
                            "</div>" +
                            "</div>" +
                            "<div class=\"row\">" +
                            "<div class=\"col-sm-12 col-xs-12\">" +
                            "<h4 class=\"m-0\">" +
                            "<small class=\"mt-10\">" + trim(value.businessCategory, " / ") + "</small>" +
                            "</h4>" +
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
                            "<hr class=\"divider-w mb-10\">" +
                            "<a class=\"text-main pull-right\" href=\"" + value.businessUrl + "\">View Detail <i class=\"fa fa-angle-double-right\"></i></a>" +
                            "</div>" +
                            "</div>" +
                            "</div>" +
                            "</div>";
                    });

                    infoWindowContent += "</div>";

                    var callbackMarkerListener = function(businessMarker, markerIndex) {

                        return function () {

                            mapInfoWindow.setOptions({
                                content: infoWindowContent,
                                maxWidth: 800,
                            });

                            mapInfoWindow.open(mapResult, markers[markerIndex]);
                        }
                    }

                    $(".place-" + businessId).on("click", callbackMarkerListener(businessMarker, markerIndex));

                    google.maps.event.addListener(businessMarker, "click", callbackMarkerListener(businessMarker, markerIndex));

                    markerBounds.extend(businessLatLng);

                    markerIndex++;
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

    $("#pjax-result-map-container").on("pjax:send", function() {
        $(".box-place").children(".overlay").show();
        $(".box-place").children(".loading-img").show();
    });

    $("#pjax-result-map-container").on("pjax:complete", function() {
        $(".box-place").children(".overlay").hide();
        $(".box-place").children(".loading-img").hide();
    });

    $("#pjax-result-map-container").on("pjax:error", function (event) {
        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>