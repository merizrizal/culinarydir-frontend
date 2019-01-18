<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use core\models\City;
use core\models\Category;
use core\models\Facility;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $id string */
/* @var $showFacilityFilter bool */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$isSearch = !empty($id) ? '-' . $id : '';
$keywordType = $keyword['searchType'];
$keywordCity = $keyword['city'];
$keywordName = $keyword['name'];
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

$productLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Product Category') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
$priceLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Price') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
$radiusLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Region') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';

if (!empty($keywordProductId)) {
    
    $productLabel = '<span class="search-field-box-placeholder">' . $keywordProductName . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
}

if ($keywordPriceMin !== null && $keywordPriceMax !== null) {
    
    $priceMinLabel = $keywordPriceMin == 0 ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($keywordPriceMin);
    $priceMaxLabel = $keywordPriceMax == 0 ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($keywordPriceMax);
    $priceLabel = '<span class="search-field-box-placeholder">' . $priceMinLabel . ' - ' . $priceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
}

if (!empty($keywordCoordinate) && !empty($keywordRadius)) {

    $radiusLabel = '<span class="search-field-box-placeholder">' . Yii::$app->formatter->asShortLength($keywordRadius) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
}

$layoutListNav = '
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'favorite') ? 'active' : '') . '">
        <a href="#favorite' . $isSearch . '" aria-controls="favorite" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Find Favourite Foods?') . '</strong></a>
    </li>
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'promo') ? 'active' : '') . '">
        <a href="#special' . $isSearch . '" aria-controls="special" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Find Specials & Discounts?') . '</strong></a>
    </li>
    <li role="presentation" class="' . ($keywordType == Yii::t('app', 'online-order') ? 'active' : '') . '">
        <a href="#order' . $isSearch . '" aria-controls="order" role="tab" data-toggle="tab"><strong>' . Yii::t('app', 'Want to Order Online?') . '</strong></a>
    </li>
';

$btnSubmitLgXs = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']);
$btnSubmitTab = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-tab']);
$btnSubmitMdSm = Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']);

$btnClearLgXs = Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'search-label lbl-clear visible-lg visible-xs']); 
$btnClearTab = Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'search-label lbl-clear visible-tab']); 
$btnClearMdSm = Html::a('<i class="fa fa-times"></i>', '', ['class' => 'search-label lbl-clear visible-md visible-sm']); ?>

<div class="search-box">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs visible-lg visible-md visible-sm" role="tablist">
        
        <?= $layoutListNav ?>
        
    </ul>
    
    <ul class="nav nav-tabs nav-stacked visible-tab" role="tablist">
        
		<?= $layoutListNav ?>

    </ul>
    
    <ul class="nav nav-tabs nav-stacked visible-xs" role="tablist">
        
        <?= $layoutListNav ?>
        
    </ul>

    <!-- Tab Favorite -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade <?= $keywordType == Yii::t('app', 'favorite') ? 'in active' : '' ?>" id="favorite<?= $isSearch ?>">

            <?= Html::beginForm(['page/result-list', 'searchType' => Yii::t('app', 'favorite'), 'city' => 'city_name'], 'get', [
                'class' => 'search-favorite'
            ]) ?>

                <div class="row">
                    <div class="col-sm-10 col-xs-12 col">
                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::dropDownList('cty', $keywordCity,
                                        ArrayHelper::map(
                                            City::find()->orderBy('name')->asArray()->all(),
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

                            <div class="col-sm-9 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::textInput('nm', $keywordName, [
                                        'class' => 'form-control input-name',
                                        'placeholder' => 'Nama Tempat / Makanan / Alamat'
                                    ]) ?>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">
                                
                                	<?php
                                	echo !empty($keywordProductId) ? $spanClear : null;
                                	echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']);
                                	echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>

                                </div>
                            </div>

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?php
                                    echo $keywordPriceMin !== null && $keywordPriceMax !== null ? $spanClear : null;
                                    echo Html::hiddenInput('pmn', $keywordPriceMin, ['class' => 'price-min']);
                                    echo Html::hiddenInput('pmx', $keywordPriceMax, ['class' => 'price-max']);
                                    echo Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

                                </div>
                            </div>

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            $column = '<div class="col-sm-4 col-tab-6 col-xs-12 col">'; ?>

                            <div class="row mb-10">
                                <div class="col-xs-12 col">
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
                                </div>
                            </div>

                        <?php
                        endif; ?>

                    </div>

                    <div class="col-sm-2 col-xs-12 col">
                        <div class="form-group">

                            <?= $btnSubmitLgXs ?>
                            <?= $btnSubmitTab ?>
                            <?= $btnSubmitMdSm ?>

                        </div>
                        <div class="btn-clear-container">

                            <?= $btnClearLgXs ?>
                            <?= $btnClearTab ?>
                            <?= $btnClearMdSm ?>

                        </div>
                    </div>
                </div>

            <?= Html::endForm() ?>

        </div>

        <div role="tabpanel" class="tab-pane fade <?= $keywordType == Yii::t('app', 'promo') ? 'in active' : '' ?>" id="special<?= $isSearch ?>">

            <?= Html::beginForm(['page/result-list', 'searchType' => Yii::t('app', 'promo'), 'city' => 'city_name'], 'get', [
                'class' => 'search-special'
            ]) ?>

                <div class="row">
                    <div class="col-sm-10 col-xs-12 col">
                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::dropDownList('cty', $keywordCity,
                                        ArrayHelper::map(
                                            City::find()->orderBy('name')->asArray()->all(),
                                            'id',
                                            function($data) {
                                                
                                                return $data['name'];
                                            }
                                        ),
                                        [
                                            'prompt' => '',
                                            'class' => 'form-control city-id',
                                            'style' => 'width: 100%'
                                        ]) ?>

                                </div>
                            </div>

                            <div class="col-sm-9 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::textInput('nm', $keywordName, [
                                        'class' => 'form-control input-name',
                                        'placeholder' => 'Nama Tempat / Makanan / Alamat'
                                    ]) ?>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-sm-offset-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

									<?php
									echo !empty($keywordProductId) ? $spanClear : null;
									echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']);
									echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>

                                </div>
                            </div>

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?php
                                    echo !empty($keywordCoordinate) && !empty($keywordRadius) ? $spanClear : null;
                                    echo Html::hiddenInput('cmp', $keywordCoordinate, ['class' => 'coordinate-map']);
                                    echo Html::hiddenInput('rmp', $keywordRadius, ['class' => 'radius-map']);
                                    echo Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 col-xs-12 col">
                        <div class="form-group">

                            <?= $btnSubmitLgXs ?>
                            <?= $btnSubmitTab ?>
                            <?= $btnSubmitMdSm ?>

                        </div>
                        <div class="btn-clear-container">

                            <?= $btnClearLgXs ?>
                            <?= $btnClearTab ?>
                            <?= $btnClearMdSm ?>

                        </div>
                    </div>
                </div>

            <?= Html::endForm() ?>

        </div>
        
        <div role="tabpanel" class="tab-pane fade <?= $keywordType == Yii::t('app', 'online-order') ? 'in active' : '' ?>" id="order<?= $isSearch ?>">

            <?= Html::beginForm(['page/result-list', 'searchType' => Yii::t('app', 'online-order'), 'city' => 'city_name'], 'get', [
                'class' => 'search-order'
            ]) ?>

                <div class="row">
                    <div class="col-sm-10 col-xs-12 col">
                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::dropDownList('cty', $keywordCity,
                                        ArrayHelper::map(
                                            City::find()->orderBy('name')->asArray()->all(),
                                            'id',
                                            function($data) {
                                                
                                                return $data['name'];
                                            }
                                        ),
                                        [
                                            'prompt' => '',
                                            'class' => 'form-control city-id',
                                            'style' => 'width: 100%'
                                        ]) ?>

                                </div>
                            </div>

                            <div class="col-sm-9 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::textInput('nm', $keywordName, [
                                        'class' => 'form-control input-name',
                                        'placeholder' => 'Nama Tempat / Makanan / Alamat'
                                    ]) ?>

                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-3 col-sm-offset-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">
                                
                                	<?php
                                	echo !empty($keywordProductId) ? $spanClear : null;
                                	echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']);
                                	echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>

                                </div>
                            </div>

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?php
                                    echo $keywordPriceMin !== null && $keywordPriceMax !== null ? $spanClear : null;
                                    echo Html::hiddenInput('pmn', $keywordPriceMin, ['class' => 'price-min']);
                                    echo Html::hiddenInput('pmx', $keywordPriceMax, ['class' => 'price-max']);
                                    echo Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

                                </div>
                            </div>

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?php
                                    echo !empty($keywordCoordinate) && !empty($keywordRadius) ? $spanClear : null;
                                    echo Html::hiddenInput('cmp', $keywordCoordinate, ['class' => 'coordinate-map']);
                                    echo Html::hiddenInput('rmp', $keywordRadius, ['class' => 'radius-map']);
                                    echo Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 col-xs-12 col">
                        <div class="form-group">

                            <?= $btnSubmitLgXs ?>
                            <?= $btnSubmitTab ?>
                            <?= $btnSubmitMdSm ?>

                        </div>
                        <div class="btn-clear-container">

                            <?= $btnClearLgXs ?>
                            <?= $btnClearTab ?>
                            <?= $btnClearMdSm ?>

                        </div>
                    </div>
                </div>

            <?= Html::endForm() ?>

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

        $(".input-name, .product-category-id, .coordinate-map, .radius-map, .price-min, .price-max").val("");
        $(".category-id").val(null).trigger("change");
        $(".facility").prop("checked", false).trigger("change");

        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".search-field-box-clear").remove();

        return false;
    });
';

$this->registerJs($jscript); ?>