<?php

use core\models\Category;
use core\models\City;
use core\models\Facility;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $keyword Array */
/* @var $pageType String */
/* @var $showFacilityFilter boolean */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$this->params['beforeEndBody'][] = function() use ($keyword, $pageType, $showFacilityFilter) {

	$keywordType = $keyword['searchType'];
	$keywordName = $keyword['name'];
	$keywordCity = $keyword['city'];
	$keywordProductId = $keyword['product']['id'];
	$keywordProductName = $keyword['product']['name'];
	$keywordCategory = $keyword['category'];
	$keywordCoordinate = $keyword['map']['coordinate'];
	$keywordRadius = $keyword['map']['radius'];
	$keywordFacility = $keyword['facility'];
	$keywordPriceMin = $keyword['price']['min'];
	$keywordPriceMax = $keyword['price']['max'];

	$spanClear = '<span class="search-field-box-clear">×</span>';

	$styleProductLabel = !empty($keywordProductId) ? 'color: #555555;' : null;
	$stylePriceLabel = $keywordPriceMin !== null && $keywordPriceMax !== null ? 'color: #555555;' : null;
	$styleRadiusLabel = !empty($keywordCoordinate) && !empty($keywordRadius) ? 'color: #555555;' : null;

	$productLabel = '<span class="search-field-box-placeholder">' . \Yii::t('app', 'Product Category') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
	$priceLabel = '<span class="search-field-box-placeholder">' . \Yii::t('app', 'Price') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
	$radiusLabel = '<span class="search-field-box-placeholder">' . \Yii::t('app', 'Region') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';

	if (!empty($keywordProductId)) {

	    $productLabel = '<span class="search-field-box-placeholder">' . $keywordProductName . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
	}

	if ($keywordPriceMin !== null && $keywordPriceMax !== null) {

	    $priceMinLabel = $keywordPriceMin == 0 ? 'Any' : \Yii::$app->formatter->asNoSymbolCurrency($keywordPriceMin);
	    $priceMaxLabel = $keywordPriceMax == 0 ? 'Any' : \Yii::$app->formatter->asNoSymbolCurrency($keywordPriceMax);
	    $priceLabel = '<span class="search-field-box-placeholder">' . $priceMinLabel . ' - ' . $priceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
	}

	if (!empty($keywordCoordinate) && !empty($keywordRadius)) {

	    $radiusLabel = '<span class="search-field-box-placeholder">' . \Yii::$app->formatter->asShortLength($keywordRadius) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
	}

	$btnSubmit = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search']);
	$btnClear = Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'btn btn-block btn-default search-label lbl-clear']); ?>

	<div class="search-box-modal" style="display:none">
		<div class="row">
			<div class="col-xs-12">
        		<div class="modal-header-search">
        			<div class="row">
        				<div class="col-md-offset-4 col-sm-offset-3 col-sm-2 col-xs-offset-1 col-xs-2">
                            <div class="input-group">
                            	<div class="input-group-addon">
                            		<button type="button" class="close btn-close text-red"><i class="fas fa-arrow-left"></i></button>
                            	</div>
                            	<span id="modal-favorite-label" class="modal-title-search">Search</span>
                        	</div>
                    	</div>
                	</div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-4 col-sm-offset-3 col-sm-8 col-tab-12 col-xs-offset-1 col-xs-11">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation">
                        <a href="#favorite" aria-controls="favorite" role="tab" data-toggle="tab" id="<?= \Yii::t('app','favorite')?>"><strong><?= \Yii::t('app', 'Favorite') ?></strong></a>
                    </li>
                    <li role="presentation">
                        <a href="#special" aria-controls="special" role="tab" data-toggle="tab" id="<?= \Yii::t('app','promo')?>"><strong><?= \Yii::t('app', 'Promo') ?></strong></a>
                    </li>
                    <li role="presentation">
                        <a href="#order" aria-controls="order" role="tab" data-toggle="tab" id="<?= \Yii::t('app','online-order')?>"><strong><?= \Yii::t('app', 'Online Order') ?></strong></a>
                    </li>
                </ul>

            	<?= Html::beginForm(['page/result-' . $pageType], 'get', [
            	    'class' => 'search-modal'
            	]) ?>

            		<?php
            		echo Html::hiddenInput('searchType', $keywordType, ['class' => 'search-type']);
            		echo Html::hiddenInput('city', strtolower($keyword['cityName'])); ?>

                	<div class="row">
                    	<div class="col-sm-9 col-tab-10 col-xs-11">
                    		<div class="form-group">

                                <?= Html::dropDownList('cty', $keywordCity,
                                    ArrayHelper::map(
                                        City::find()->orderBy('name')->asArray()->andWhere(['name' => 'Bandung'])->all(),
                                        'id',
                                        function($data) {

                                            return $data['name'];
                                        }
                                    ),
                                    [
                                        'prompt' => '',
                                        'class' => 'form-control city-id',
                                        'style' => 'width: 100%',
                                    ]) ?>

                            </div>
                        </div>
                    </div>

        			<div class="row">
                        <div class="col-sm-9 col-tab-10 col-xs-11">
                            <div class="form-group">

								<?php
                                echo Html::textInput('nm', $keywordName, [
                                    'class' => 'form-control search-input-modal',
                                    'placeholder' => 'Nama Tempat / Makanan / Alamat',
                                    'data-keyword' => $keyword,
                                    'data-type' => 'favorit'
                                ]);

                                echo !empty($keywordName) ? $spanClear : null; ?>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 col-tab-10 col-xs-11">
                            <div class="form-group">

                            	<?php
                            	echo !empty($keywordProductId) ? $spanClear : null;
                            	echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']);
                            	echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>

                            </div>
                        </div>
                    </div>

					<div class="row">
                        <div class="col-sm-9 col-tab-10 col-xs-11">
                            <div class="form-group">

                                <?= Html::dropDownList('ctg', $keywordCategory,
                                    ArrayHelper::map(
                                        Category::find()->orderBy('name')->asArray()->all(),
                                        'id',
                                        function($data) {

                                            return $data['name'];
                                        }
                                    ),
                                    [
                                        'prompt' => '',
                                        'class' => 'form-control category-id',
                                        'style' => 'width: 100%'
                                    ]) ?>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 col-tab-10 col-xs-11">
                            <div class="form-group">

                                <?php
                                echo $keywordPriceMin !== null && $keywordPriceMax !== null ? $spanClear : null;
                                echo Html::hiddenInput('pmn', $keywordPriceMin, ['class' => 'price-min']);
                                echo Html::hiddenInput('pmx', $keywordPriceMax, ['class' => 'price-max']);
                                echo Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 col-tab-10 col-xs-11">
                            <div class="form-group">

                                <?php
                                echo !empty($keywordCoordinate) && !empty($keywordRadius) ? $spanClear : null;
                                echo Html::hiddenInput('cmp', $keywordCoordinate, ['class' => 'coordinate-map']);
                                echo Html::hiddenInput('rmp', $keywordRadius, ['class' => 'radius-map']);
                                echo Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]); ?>

                            </div>
                        </div>
                    </div>

                    <?php
                    if ($showFacilityFilter):

                        $modelFacility = Facility::find()
                            ->orderBy('name')
                            ->asArray()->all();

                        $colDivider = ceil(count($modelFacility) / 3);

                        $column = '<div class="col-sm-4 col-tab-6 col-xs-6 col">'; ?>

                        <a class="search-label more-option" data-toggle="collapse" href=".facility-collapse">More Options
                            <span class="fa fa-chevron-circle-down"></span>
                        </a>
                        <div class="facility-collapse collapse <?= !empty($keywordFacility) ? 'in' : '' ?>">
                        	<div class="form-group">
                            	<div class="row mt-10">

                                    <?= Html::checkboxList('fct', $keywordFacility,
                                        ArrayHelper::map(
                                            $modelFacility,
                                            'id',
                                            function($data) {

                                                return $data['name'];
                                            }
                                        ),
                                        [
                                            'item' => function ($index, $label, $name, $checked, $value) use ($colDivider, $column, $modelFacility) {

                                                $checkboxes = '
                                                    <div class="row">
                                                        <div class="col-xs-12 col">
                                                            <label>' .
                                                                Html::checkbox($name, $checked, [
                                                                    'value' => $value,
                                                                    'class' => 'facility icheck',
                                                                ]) . ' ' . $label .
                                                            '</label>
                                                        </div>
                                                    </div>
                                                ';

                                                $index++;

                                                if ($index === 1) {

                                                    return $column . $checkboxes;
                                                } else if ($index === count($modelFacility)) {

                                                    return $checkboxes . '</div>';
                                                } else if ($index % $colDivider === 0) {

                                                    return $checkboxes . '</div>' . $column;
                                                } else {

                                                    return $checkboxes;
                                                }
                                            }
                                        ]) ?>

                                </div>
                            </div>
                        </div>

                    <?php
                    endif; ?>

                    <div class="row mt-10">
                    	<div class="col-sm-4 col-tab-4 col-xs-5">
                            <div class="form-group">
                                <?= $btnSubmit ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-tab-4 col-xs-offset-1 col-xs-5 visible-lg visible-md visible-sm visible-xs">
                            <div class="btn-clear-container">
                                <?= $btnClear ?>
                            </div>
                        </div>
                        <div class="col-tab-4 col-xs-offset-2 visible-tab">
                            <div class="btn-clear-container">
                                <?= $btnClear ?>
                            </div>
                        </div>
                    </div>

                <?= Html::endForm(); ?>

            </div>
        </div>
    </div>

	<?php
    Modal::begin([
        'header' => 'Coming Soon',
        'id' => 'modal-coming-soon',
        'size' => Modal::SIZE_SMALL,
    ]);

        echo 'Fitur ini akan segera hadir';

    Modal::end();

    Modal::begin([
        'header' => \Yii::t('app', 'Product Category'),
        'id' => 'modal-product-category',
    ]);

        echo '
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">

                    ' . Html::textInput('product_category_search', null, ['class' => 'form-control input-product-category', 'placeholder' => 'Cari kategori menu di sini...']) . '

                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="overlay" style="display: none"></div>
                <div class="loading-img" style="display: none"></div>
                <div id="modal-content"></div>
            </div>
        ';

    Modal::end();

    Modal::begin([
        'header' => \Yii::t('app', 'Average Spending'),
        'id' => 'modal-price',
        'footer' => '
            <div class="row">
                <div class="col-md-2 col-md-offset-10 col-sm-3 col-sm-offset-9 col-xs-12">
                    <button class="btn btn-block btn-round btn-d btn-price-confirm">Ok</button>
                </div>
            </div>
        ',
    ]);

        $priceItems = [
            '0' => \Yii::t('app', 'Any Price'), '20000' => '20.000', '40000' => '40.000', '60000' => '60.000', '80000' => '80.000',
            '100000' => '100.000', '120000' => '120.000', '140000' => '140.000', '160000' => '160.000',
            '180000' => '180.000', '200000' => '200.000', '220000' => '220.000', '240000' => '240.000',
            '260000' => '260.000', '280000' => '280.000', '300000' => '300.000'
        ];

        echo '
            <div class="row">
                <div class="col-sm-5 col-tab-5 col-xs-12">'

                    . \Yii::t('app', \Yii::t('app', 'Price Min'))

                    . Html::dropDownList('price_min', null, $priceItems, [
                        'prompt' => '',
                        'class' => 'form-control price-min-select',
                        'style' => 'width: 100%',
                    ]) . '

                </div>

                <div class="col-sm-2 col-tab-2 col-xs-12 mt-30 visible-lg visible-md visible-sm visible-tab hidden-xs text-center"> - </div>

                <div class="col-sm-5 col-tab-5 col-xs-12">'

                    . \Yii::t('app', \Yii::t('app', 'Price Max'))

                    . Html::dropDownList('price_max', null, $priceItems, [
                        'prompt' => '',
                        'class' => 'form-control price-max-select',
                        'style' => 'width: 100%',
                    ]) . '

                </div>
            </div>
        ';

    Modal::end();

    Modal::begin([
        'header' => \Yii::t('app', 'Region'),
        'id' => 'modal-region',
        'footer' => '
            <div class="row">
                <div class="col-md-10 col-sm-10 col-xs-12 text-left">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-radius-500">
                            <input class="radius-500" type="radio" name="radius" data-radius="500" autocomplete="off" checked> 500 m
                        </label>
                        <label class="btn btn-radius-1000">
                            <input class="radius-1000" type="radio" name="radius" data-radius="1000" autocomplete="off"> 1 km
                        </label>
                        <label class="btn btn-radius-2000">
                            <input class="radius-2000" type="radio" name="radius" data-radius="2000" autocomplete="off"> 2 km
                        </label>
                    </div>
                </div>
                <div class="col-sm-2 col-md-2 col-xs-12">
                    <button class="btn btn-block btn-round btn-d btn-coordinate-confirm">Ok</button>
                </div>
            </div>
        ',
    ]);

        echo '<div id="map"></div>';

    Modal::end();
};

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.css', ['depends' => 'yii\web\YiiAsset']);

$cssScript = '
    #map .map-marker {
        position: absolute;
        background: url(' . \Yii::$app->request->baseUrl . '/media/img/marker.png) no-repeat;
        background-size: 100% 100%;
        height: 32px;
        width: 32px;
        top: 50%;
        left: 50%;
        z-index: 1;
        margin-left: -15px;
        margin-top: -32px;
        cursor: pointer;
    }
';

$this->registerCss($cssScript);

frontend\components\GrowlCustom::widget();

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDORji7AXzhxgYhuKOGJg6_KYrnTPYPOn8', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var btnProductCategory = null;
    var btnPrice = null;
    var priceMin = null;
    var priceMinValue = null;
    var priceMax = null;
    var priceMaxValue = null;
    var typingTimer;
    var typingInterval = 500;

    function initMap(btnRegion, coordinateMap, radiusMap) {

        var executeMap = function(defaultLatLng) {

            var mapOptions = {
                center: defaultLatLng,
                zoom: 15,
                disableDefaultUI: true,
                gestureHandling: "greedy",
                styles: [ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] }, { "featureType": "poi.park", "elementType": "labels.text", "stylers": [ { "visibility": "off" } ] } ],
            }

            var map = new google.maps.Map(document.getElementById("map"), mapOptions);

            var icon = {
                url: "' . \Yii::$app->request->baseUrl . '/media/img/marker.png",
                scaledSize: new google.maps.Size(32, 32),
                origin: new google.maps.Point(0,0),
                anchor: new google.maps.Point(15, 32)
            };

            var marker = new google.maps.Marker({
                position: defaultLatLng,
                map: map,
                icon: icon
            });

            var radius = 500;
            var latitude = map.getCenter().lat();
            var longitude = map.getCenter().lng();

            var circleRadius = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#e52626",
                fillOpacity: 0.35,
                radius: radius,
            });

            $("<div/>").addClass("map-marker").appendTo(map.getDiv());

            circleRadius.setMap(map);
            circleRadius.bindTo("center", marker, "position");

            google.maps.event.addListener(map, "dragstart", function() {

                marker.setMap(null);
                circleRadius.setMap(null);
            });

            google.maps.event.addListener(map, "dragend", function() {

                latitude = map.getCenter().lat();
                longitude = map.getCenter().lng();

                map.panTo(new google.maps.LatLng(latitude, longitude));

                marker.setPosition({lat: latitude, lng: longitude});
                circleRadius.setMap(map);
                circleRadius.bindTo("center", marker, "position");
            });

            google.maps.event.addListener(map, "zoom_changed", function() {

                marker.setMap(null);
                circleRadius.setMap(null);

                latitude = map.getCenter().lat();
                longitude = map.getCenter().lng();

                map.panTo(new google.maps.LatLng(latitude, longitude));

                marker.setPosition({lat: latitude, lng: longitude});
                circleRadius.setMap(map);
                circleRadius.bindTo("center", marker, "position");
            });

            $(".radius-500").on("change", function() {

                radius = parseInt($(this).data("radius"));

                map.setZoom(15);
                circleRadius.setRadius(radius);
            });

            $(".radius-1000").on("change", function() {

                radius = parseInt($(this).data("radius"));

                map.setZoom(14);
                circleRadius.setRadius(radius);
            });

            $(".radius-2000").on("change", function() {

                radius = parseInt($(this).data("radius"));

                map.setZoom(13);
                circleRadius.setRadius(radius);
            });

            if (radiusMap.val() != "") {

                radius = parseInt(radiusMap.val());

                if (radius == 2000) {

                    map.setZoom(13);
                    circleRadius.setRadius(radius);
                } else if (radius == 1000) {

                    map.setZoom(14);
                    circleRadius.setRadius(radius);
                } else {

                    map.setZoom(15);
                    circleRadius.setRadius(500);
                }

                $(".btn-radius-" + radiusMap.val()).trigger("click");
            }

            $(".btn-coordinate-confirm").on("click", function() {

                radiusMap.val(radius);
                coordinateMap.val(latitude + ", " + longitude);

                if (coordinateMap.val() != "" && radiusMap.val() != "") {

                    btnRegion.html($(".btn-radius-" + radiusMap.val()).text() + " <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#555555");

                    if (btnRegion.parent().find(".search-field-box-clear").length == 0) {

                        btnRegion.parent().append("<span class=\"search-field-box-clear\">×</span>");
                    }
                }

                $("#modal-region").modal("hide");
            });
        };

        var latLng = {lat: -6.9175, lng: 107.6191};

        if (coordinateMap.val() != "") {

            var mapLatLng = coordinateMap.val().split(",");
            latLng = {lat: parseFloat(mapLatLng[0]), lng: parseFloat(mapLatLng[1])};

            executeMap(latLng);
        } else {

            executeMap(latLng);

            var navigatorGeolocation = navigator.geolocation;

            if (navigatorGeolocation) {

                navigatorGeolocation.getCurrentPosition(function(position) {

                    executeMap({lat: position.coords.latitude, lng: position.coords.longitude});
                }, function(error) {

                    messageResponse("aicon aicon-icon-info", "Maps error", error.message, "danger");
                });
            }
        }
    };

    $(".city-id").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'City') . '",
        minimumResultsForSearch: Infinity
    });

    $(".category-id").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'Business Category') . '",
        minimumResultsForSearch: Infinity,
        allowClear: true
    });

    $(".price-min-select").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'Price Min') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $(".price-max-select").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'Price Max') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $(".btn-product-category").on("click", function() {

        btnProductCategory = $(this);

        $("#modal-product-category").modal("show");
        $("#modal-product-category").find(".overlay").show();
        $("#modal-product-category").find(".loading-img").show();

        return false;
    });

    $("#modal-product-category").on("shown.bs.modal", function(e) {

        $.ajax({
            cache: false,
            type: "POST",
            url: "' . \Yii::$app->urlManager->createUrl(['data/product-category']) . '",
            success: function(response) {

                $("#modal-product-category").find("#modal-content").html(response);

                $("#modal-product-category").find(".overlay").hide();
                $("#modal-product-category").find(".loading-img").hide();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                $("#modal-product-category").find(".overlay").hide();
                $("#modal-product-category").find(".loading-img").hide();
            }
        });
    });

    $("#modal-product-category").on("click", ".product-category-name", function() {

        $(".input-product-category").val("");
        btnProductCategory.siblings(".product-category-id").val($(this).data("id"));

        if (btnProductCategory.siblings(".product-category-id").val() != "") {

            btnProductCategory.html("<span class=\"search-field-box-placeholder\">" + $(this).html() + " </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#555555");

            if (btnProductCategory.parent().find(".search-field-box-clear").length == 0) {

                btnProductCategory.parent().append("<span class=\"search-field-box-clear\">×</span>");
            }
        }

        $("#modal-product-category").modal("hide");

        return false;
    });

    $(".input-product-category").on("keyup", function() {

        thisObj = $(this);

        clearTimeout(typingTimer);

        $("#modal-product-category").find(".overlay").show();
        $("#modal-product-category").find(".loading-img").show();

        typingTimer = setTimeout(function() {

            $.ajax({
                cache: false,
                type: "POST",
                data: {keyword: thisObj.val()},
                url: "' . \Yii::$app->urlManager->createUrl(['data/product-category']) . '",
                success: function(response) {

                    $("#modal-product-category").find("#modal-content").html(response);

                    $("#modal-product-category").find(".overlay").hide();
                    $("#modal-product-category").find(".loading-img").hide();
                },
                error: function(xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                    $("#modal-product-category").find(".overlay").hide();
                    $("#modal-product-category").find(".loading-img").hide();
                }
            });
        }, typingInterval);
    });

    $(".btn-price").on("click", function() {

        btnPrice = $(this);
        priceMin = btnPrice.siblings(".price-min");
        priceMax = btnPrice.siblings(".price-max");

        if (priceMin.val() != "") {

            $(".price-min-select").val(priceMin.val()).trigger("change");
        } else {

            $(".price-min-select").val("0").trigger("change");
        }

        if (priceMax.val() != "") {

            $(".price-max-select").val(priceMax.val()).trigger("change");
        } else {

            $(".price-max-select").val("0").trigger("change");
        }

        $("#modal-price").modal("show");

        return false;
    });

    $(".price-min-select").on("change", function() {

        priceMinValue = $(this).val();
    });

    $(".price-max-select").on("change", function() {

        priceMaxValue = $(this).val();
    });

    $(".btn-price-confirm").on("click", function() {

        if ($(".price-min-select").val() == "") {

            $(".price-min-select").val("0").trigger("change");
        }

        if ($(".price-max-select").val() == "") {

            $(".price-max-select").val("0").trigger("change");
        }

        priceMin.val(priceMinValue);
        priceMax.val(priceMaxValue);

        priceMinLabel = $(".price-min-select option:selected").text();
        priceMaxLabel = $(".price-max-select option:selected").text();

        if (priceMin.val() != "" && priceMax.val() != "") {

            btnPrice.html("<span class=\"search-field-box-placeholder\">" + priceMinLabel + " - " + priceMaxLabel + " </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#555555");

            if (btnPrice.parent().find(".search-field-box-clear").length == 0) {

                btnPrice.parent().append("<span class=\"search-field-box-clear\">×</span>");
            }
        }

        $("#modal-price").modal("hide");
    });

    $(".btn-region").on("click", function() {

        btnRegion = $(this);
        coordinateMap = btnRegion.siblings(".coordinate-map");
        radiusMap = btnRegion.siblings(".radius-map");

        if (radiusMap.val() == "") {

            $("#modal-region").find(".btn-radius-500").trigger("click");
        }

        initMap(btnRegion, coordinateMap, radiusMap);

        $("#modal-region").modal("show");

        return false;
    });

    $(".btn-product-category").parent().on("click", ".search-field-box-clear", function() {

        $(".product-category-id").val("");
        $(".btn-product-category").html("' . \Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-product-category").siblings(".search-field-box-clear").remove();

        return false;
    });

    $(".btn-price").parent().on("click", ".search-field-box-clear", function() {

        $(".price-min, .price-max").val("");
        $(".btn-price").html("' . \Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").siblings(".search-field-box-clear").remove();

        return false;
    });

    $(".btn-region").parent().on("click", ".search-field-box-clear", function() {

        $(".coordinate-map, .radius-map").val("");
        $(".btn-region").html("' . \Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-radius-500").trigger("click");
        $(".btn-region").siblings(".search-field-box-clear").remove();

        return false;
    });

    $(".search-input-modal").parent().on("click", ".search-field-box-clear", function() {

        $(".search-input-modal, .search-input").val("");
        $(".search-input-modal").siblings(".search-field-box-clear").remove();

        return false;
    });

    $(".lbl-clear").on("click", function() {

        $(".search-input, .search-input-modal, .product-category-id, .coordinate-map, .radius-map, .price-min, .price-max").val("");
        $(".category-id").val(null).trigger("change");
        $(".facility").prop("checked", false).trigger("change");

        $(".btn-product-category").html("' . \Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . \Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . \Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".search-field-box-clear").remove();

        return false;
    });

    $(".filter-input").on("click", function() {

        var navTab;
        var navTabId;
        var inputPrice;
        var inputBusinessCategory;
        var moreFacility;
        var facilityCollapse;

        $(".search-box > .nav-tabs").children("li").each(function() {

            if ($(this).hasClass("active")) {

                navTab = $(this).children().attr("id");
                return false;
            }
        });

        $(".search-box-modal").find(".nav-tabs").children("li").each(function() {

            var thisObj = $(this);
            var navTabModal = thisObj.children("a").attr("id");

            inputPrice = $(".search-modal").find(".btn-price").parent();
            inputBusinessCategory = $(".search-modal").find(".category-id").parent();
            moreFacility = $(".search-modal").find(".more-option");
            facilityCollapse = moreFacility.siblings(".facility-collapse");

            if (navTabModal != navTab) {

                thisObj.removeClass("active");
            } else {

                thisObj.addClass("active");
            }

            if (navTab == "' . \Yii::t('app','favorite') . '") {

                inputPrice.removeClass("hidden");
                inputBusinessCategory.removeClass("hidden");
                moreFacility.removeClass("hidden");
                facilityCollapse.removeClass("hidden");
            } else if (navTab == "' . \Yii::t('app','promo') . '") {

                inputPrice.addClass("hidden");
                inputBusinessCategory.removeClass("hidden");
                moreFacility.addClass("hidden");
                facilityCollapse.addClass("hidden");
            } else if (navTab == "' . \Yii::t('app','online-order') . '") {

                inputPrice.removeClass("hidden");
                inputBusinessCategory.addClass("hidden");
                moreFacility.addClass("hidden");
            }

            thisObj.on("click", function() {

                $(".search-box > .nav-tabs").children("li").each(function() {

                    if ($(this).children().attr("id") != navTabModal) {

                        $(this).removeClass("active");
                    } else {

                        $(this).addClass("active");
                    }

                    $(this).parent().siblings("form").find(".search-type").val(navTabModal);
                });

                $(this).parent().siblings("form").find(".search-type").val(navTabModal);

                if (navTabModal == "' . \Yii::t('app','favorite') . '") {

                    inputPrice.removeClass("hidden");
                    inputBusinessCategory.removeClass("hidden");
                    moreFacility.removeClass("hidden");
                    facilityCollapse.removeClass("hidden");
                } else if (navTabModal == "' . \Yii::t('app','promo') . '") {

                    inputPrice.addClass("hidden");
                    inputBusinessCategory.removeClass("hidden");
                    moreFacility.addClass("hidden");
                    facilityCollapse.addClass("hidden").removeClass("in");
                } else if (navTabModal == "' . \Yii::t('app','online-order') . '") {

                    inputPrice.removeClass("hidden");
                    inputBusinessCategory.addClass("hidden");
                    moreFacility.addClass("hidden");
                    facilityCollapse.addClass("hidden").removeClass("in");
                }
            });

            thisObj.parent().siblings("form").find(".search-type").val(navTab);
        });

        $(".search-box-modal").fadeIn("medium");
    });

    $(".search-box").find(".nav-tabs").children("li").on("click", function() {

        var idNavTab = $(this).children().attr("id");

        $(this).parent().siblings("form").find(".search-type").val(idNavTab);
    });

    if ($(".btn-search-map-toggle").length) {

        var keyword = $(".btn-search-map-toggle").data("keyword");

        if (keyword == "favorit") {

            keyword = "favorite";
        } else if (keyword == "promo") {

            keyword = "special";
        } else if (keyword == "pesan-online") {

            keyword = "order";
        }

        $(".search-box-modal").find("#" + keyword + "-id").parent().addClass("active");
    }

    $(".btn-search-map-toggle").on("click", function() {

        $(".search-box-modal").fadeIn("medium");
    });

    $(".btn-close").on("click", function() {

        $(".search-box-modal").fadeOut("medium");
    });

    $(".search-input-modal").on("keyup", function() {

        if ($(this).val() != "") {

            if ($(this).siblings(".search-field-box-clear").length == 0) {

                $(".search-input-modal").parent().append("<span class=\"search-field-box-clear\">×</span>");
            }

            $(".search-input").val($(this).val());
        } else {

            $(".search-input, .search-input-modal").val("");
            $(".search-input-modal").siblings(".search-field-box-clear").remove();
        }

        return false;
    });

    $(".search-input").on("keyup", function() {

        if ($(this).val() != "") {

            if ($(".search-input-modal").siblings(".search-field-box-clear").length == 0) {

                $(".search-input-modal").parent().append("<span class=\"search-field-box-clear\">×</span>");
            }

            $(".search-input-modal").val($(this).val());
        } else {

            $(".search-input-modal").val("");
            $(".search-input-modal").siblings(".search-field-box-clear").remove();
        }

        return false;
    });
';

$this->registerJs($jscript); ?>