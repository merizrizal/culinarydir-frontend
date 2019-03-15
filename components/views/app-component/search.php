<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $showFacilityFilter bool */
/* @var $type string */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$keywordType = $keyword['searchType'];
$keywordName = $keyword['name'];

$layoutListNav = '
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'favorite') ? 'active' : '') . '">
        <a href="#favorite" aria-controls="favorite" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Favorite') . '</strong></a>
    </li>
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'promo') ? 'active' : '') . '">
        <a href="#special" aria-controls="special" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Promo') . '</strong></a>
    </li>
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'online-order') ? 'active' : '') . '">
        <a href="#order" aria-controls="order" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Online Order') . '</strong></a>
    </li>
'; ?>

<div class="search-box <?= !empty($type) ? $type : "" ?>">
    
    <ul class="nav nav-tabs" role="tablist">
        <?= $layoutListNav ?>
    </ul>
    
    <div class="form-group">
        <div class="input-group">
        	<div class="input-group-addon">
        		<i class="fa fa-search"></i>
        	</div>
        	<?= Html::textInput('nm', $keywordName, ['class' => 'form-control search-input', 'placeholder' => 'Nama Tempat / Makanan / Alamat']) ?>
    	</div>
	</div>
</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".city-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City') . '",
        minimumResultsForSearch: Infinity
    });

    $(".category-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
        minimumResultsForSearch: Infinity,
        allowClear: true
    });

    $(".search-favorite, .search-special, .search-order").on("submit", function() {

        var action = $(this).attr("action").replace("city_name", $(this).find(".city-id").find(":selected")[0].label.toLowerCase().replace(" ", "-"));

        $(this).attr("action", action);
    });

    $(".lbl-clear").on("click", function() {

        $(".search-input, .input-name, .product-category-id, .coordinate-map, .radius-map, .price-min, .price-max").val("");
        $(".category-id").val(null).trigger("change");
        $(".facility").prop("checked", false).trigger("change");

        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".search-field-box-clear").remove();

        return false;
    });

    $(".search-input").on("click", function() {
        
        $(".search-input").attr("disabled", "disabled");

        $(".search-box-modal").fadeIn("medium");

        var href;

        $(".search-box > .nav-tabs").children("li").each(function() {
            
            if ($(this).hasClass("active")) {
                
                href = $(this).children().attr("href");
                return false;
            }
        });
        
        $(".search-box-modal").find(".nav-tabs").children("li").each(function() {
        
            if ($(this).children("a").attr("href") != ("#" + href)) {

                $(this).removeClass("active");
            }
        });

        $(".search-box-modal").find(href + "-id").parent().addClass("active");
    });

    $(".search-box-modal").find(".nav-tabs").children("li").each(function() {
        
        var thisObj = $(this);

        thisObj.on("click", function() {
            
            var modalTabsLink = thisObj.children().attr("href");
            
            $(".search-box > .nav-tabs").children("li").each(function() {

                if ($(this).children().attr("href") != modalTabsLink) {

                    $(this).removeClass("active");
                } else {

                    $(this).addClass("active");
                }
            });
        });
    });
';

$this->registerJs($jscript); ?>