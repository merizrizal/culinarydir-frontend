<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->params['beforeEndBody'][] = function() {

    Modal::begin([
        'header' => 'Coming Soon',
        'id' => 'modal-coming-soon',
        'size' => Modal::SIZE_SMALL,
    ]);

    echo 'Fitur ini akan segera hadir';

    Modal::end();

    Modal::begin([
        'header' => Yii::t('app', 'Product Category'),
        'id'     => 'modal-product-category',
    ]);

    echo '
        <div class="overlay" style="display: none"></div>
        <div class="loading-img" style="display: none"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                ' . Html::textInput('product_category_search', null, ['class' => 'form-control input-product-category', 'placeholder' => 'Cari kategori menu disini...']) . '
            </div>
        </div>
        <div id="modal-content"></div>
    ';

    Modal::end();

    Modal::begin([
        'header' => Yii::t('app', 'Average Spending'),
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
        '0' => 'Any', '20000' => '20.000', '40000' => '40.000', '60000' => '60.000', '80000' => '80.000',
        '100000' => '100.000', '120000' => '120.000', '140000' => '140.000', '160000' => '160.000',
        '180000' => '180.000', '200000' => '200.000', '220000' => '220.000', '240000' => '240.000',
        '260000' => '260.000', '280000' => '280.000', '300000' => '300.000'
    ];

    echo '
        <div class="row">
            <div class="col-sm-5 col-tab-5 col-xs-12">
        ';

    echo Yii::t('app', Yii::t('app', 'Price Min'));

    echo Html::dropDownList('price_min', null, $priceItems, [
        'prompt' => '',
        'class' => 'form-control price-min-select',
        'style' => 'width: 100%',
    ]);

    echo '
            </div>
        ';

    echo '<div class="col-sm-2 col-tab-2 col-xs-12 mt-30 visible-lg visible-md visible-sm visible-tab hidden-xs text-center"> - </div>';

    echo '
            <div class="col-sm-5 col-tab-5 col-xs-12">
                <div class="form-group">
        ';

    echo Yii::t('app', Yii::t('app', 'Price Max'));

    echo Html::dropDownList('price_max', null, $priceItems, [
        'prompt' => '',
        'class' => 'form-control price-max-select',
        'style' => 'width: 100%',
    ]);

    echo '
                </div>
            </div>
        </div>
        ';

    Modal::end();

    Modal::begin([
        'header' => Yii::t('app', 'Region'),
        'id'     => 'modal-region',
        'footer' => '
            <div class="row">
                <div class="col-md-10 col-sm-10 col-xs-12 text-left mb-10">
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

$csscript = '
    #map .map-marker {
        position: absolute;
        background: url(' . Yii::$app->request->baseUrl . '/media/img/marker.png) no-repeat;
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

$this->registerCss($csscript);

frontend\components\GrowlCustom::widget();

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC84sFxZL4KCPIFl8ezsta45Rm8WPRIM7Y', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var btnProductCategory = null;
    var btnPrice = null;
    var priceMin = null;
    var priceMinValue = null;
    var priceMax = null;
    var priceMaxValue = null;
    var btnRegion = null;
    var coordinateMap = null;
    var radiusMap = null;

    var initMap = function() {

        if (coordinateMap.val() != "") {

            var keyCoordinate = coordinateMap.val().split(",");
            defaultLatLng = {lat: parseFloat(keyCoordinate[0].trim()), lng: parseFloat(keyCoordinate[1].trim())};
        } else {

            var defaultLatLng = {lat: -6.9175, lng: 107.6191};
        }

        var mapOptions = {
            center: defaultLatLng,
            zoom: 15,
            disableDefaultUI: true,
            gestureHandling: "greedy",
            styles: [ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] }, { "featureType": "poi.park", "elementType": "labels.text", "stylers": [ { "visibility": "off" } ] } ],
        }

        var map = new google.maps.Map(document.getElementById("map"), mapOptions);

        var radius = 500;
        var latitude = map.getCenter().lat();
        var longitude = map.getCenter().lng();

        $("<div/>").addClass("map-marker").appendTo(map.getDiv());

        var icon = {
            url: "' . Yii::$app->request->baseUrl . '/media/img/marker.png",
            scaledSize: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0,0),
            anchor: new google.maps.Point(15, 32)
        };

        var marker = new google.maps.Marker({
            position: defaultLatLng,
            map: map,
            icon: icon
        });

        var circleRadius = new google.maps.Circle({
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#e52626",
            fillOpacity: 0.35,
            radius: radius,
        });

        circleRadius.setMap(map);
        circleRadius.bindTo("center", marker, "position");

        google.maps.event.addListener(map, "dragstart", function() {

            marker.setMap(null);
            circleRadius.setMap(null);
        });

        google.maps.event.addListener(map, "dragend", function() {

            latitude = map.getCenter().lat();
            longitude = map.getCenter().lng();

            map.panTo(new google.maps.LatLng(latitude,longitude));

            marker.setPosition({lat: latitude, lng: longitude});
            circleRadius.setMap(map);
            circleRadius.bindTo("center", marker, "position");
        });

        google.maps.event.addListener(map, "zoom_changed", function() {

            marker.setMap(null);
            circleRadius.setMap(null);

            latitude = map.getCenter().lat();
            longitude = map.getCenter().lng();

            map.panTo(new google.maps.LatLng(latitude,longitude));

            marker.setPosition({lat: latitude, lng: longitude});
            circleRadius.setMap(map);
            circleRadius.bindTo("center", marker, "position");
        });

        $(".radius-500").on("ifChecked", function() {

            radius = parseInt($(this).attr("data-radius"));

            map.setZoom(15);
            circleRadius.setRadius(radius);
        });

        $(".radius-1000").on("ifChecked", function() {

            radius = parseInt($(this).attr("data-radius"));

            map.setZoom(14);
            circleRadius.setRadius(radius);
        });

        $(".radius-2000").on("ifChecked", function() {

            radius = parseInt($(this).attr("data-radius"));

            map.setZoom(13);
            circleRadius.setRadius(radius);
        });

        if (radiusMap.val() != "") {

            radius = parseInt(radiusMap.val());

            if (radius == 500) {

                map.setZoom(15);
                circleRadius.setRadius(radius);
            } else if (radius == 1000) {

                map.setZoom(14);
                circleRadius.setRadius(radius);
            } else if (radius == 2000) {

                map.setZoom(13);
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

            if (!coordinateMap.val()) {

                btnRegion.html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>");
            } else {

                btnRegion.html($(".btn-radius-" + radiusMap.val()).text() + " <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#555555");

                if (btnRegion.parent().find(".search-field-box-clear").length == 0) {
                    btnRegion.parent().append("<span class=\"search-field-box-clear\">×</span>");
                }

                btnRegion.parent().find(".search-field-box-clear").off("click");

                btnRegion.parent().find(".search-field-box-clear").on("click", function() {

                    coordinateMap.val("");
                    radiusMap.val("");
                    btnRegion.html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
                    $(this).remove();
                });
            }

            $("#modal-region").modal("hide");
        });
    };

    $(".btn-product-category").on("click", function() {

        btnProductCategory = $(this);

        if (btnProductCategory.siblings(".product-category-id").val() == "") {

            $("#modal-product-category").find(".input-product-category").val("");
        }

        $("#modal-product-category").modal("show");

        return false;
    });

    $("#modal-product-category").on("show.bs.modal", function(e) {

        $("#modal-product-category").find("#modal-content").html("");

        $("#modal-product-category").find(".overlay").show();
        $("#modal-product-category").find(".loading-img").show();
    });

    $("#modal-product-category").on("shown.bs.modal", function(e) {

        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl(['data/product-category']) . '",
            success: function(response) {

                $("#modal-product-category").find("#modal-content").html(response);

                $("#modal-product-category").find(".overlay").hide();
                $("#modal-product-category").find(".loading-img").hide();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $("#modal-product-category").on("click", ".product-category-name", function() {

        var id = $(this).attr("data-id");
        var name = $(this).html();

        btnProductCategory.html("<span class=\"search-field-box-placeholder\">" + name + " </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#555555");
        btnProductCategory.siblings(".product-category-id").val(id);

        $("#modal-product-category").modal("hide");
        $("#modal-product-category").find(".input-product-category").val("");

        if (btnProductCategory.parent().find(".search-field-box-clear").length == 0) {

            btnProductCategory.parent().append("<span class=\"search-field-box-clear\">×</span>");
        }

        btnProductCategory.parent().find(".search-field-box-clear").off("click");

        btnProductCategory.parent().find(".search-field-box-clear").on("click", function() {

            btnProductCategory.siblings(".product-category-id").val("");
            btnProductCategory.html("<span class=\"search-field-box-placeholder\">' . Yii::t('app', 'Product Category') . ' </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
            $(this).remove();
        });

        return false;
    });

    $(".input-product-category").on("keyup", function() {

        $.ajax({
            cache: false,
            type: "POST",
            data: {keyword: $(this).val()},
            url: "' . Yii::$app->urlManager->createUrl(['data/product-category']) . '",
            success: function(response) {

                $("#modal-product-category").find("#modal-content").html(response);

                $("#modal-product-category").find(".overlay").hide();
                $("#modal-product-category").find(".loading-img").hide();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $(".price-min-select").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Price Min') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $(".price-max-select").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Price Max') . '",
        minimumResultsForSearch: -1,
        allowClear: true
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

            btnPrice.parent().find(".search-field-box-clear").off("click");

            btnPrice.parent().find(".search-field-box-clear").on("click", function() {

                $(".price-min-select").val(null).trigger("change");
                $(".price-max-select").val(null).trigger("change");

                btnPrice.siblings(".price-min, .price-max").val("");
                btnPrice.html("<span class=\"search-field-box-placeholder\">' . Yii::t('app', 'Price') . ' </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
                $(this).remove();
            });
        } else {

            btnPrice.html("<span class=\"search-field-box-placeholder\">' . Yii::t('app', 'Price') . ' </span><span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
            btnPrice.parent().find(".search-field-box-clear").remove();
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

        initMap();

        $("#modal-region").modal("show");

        return false;
    });

    $("#modal-region").on("shown.bs.modal", function() {

        google.maps.event.trigger(map, "resize");
    });
';

$this->registerJs($jscript); ?>