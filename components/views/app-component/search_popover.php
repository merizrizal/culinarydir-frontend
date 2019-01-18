<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\popover\PopoverX;
use core\models\City;
use core\models\Category;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $popover bool */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$keywordType = $keyword['searchType'];
$keywordCity = $keyword['city'];
$keywordName = $keyword['name'];
$keywordProductId = $keyword['product']['id'];
$keywordProductName = $keyword['product']['name'];
$keywordCategory = $keyword['category'];
$keywordCoordinate = $keyword['map']['coordinate'];
$keywordRadius = $keyword['map']['radius'];
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

$btnSubmitLgXs = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']);
$btnSubmitTab = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-tab']);
$btnSubmitMdSm = Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']);

$btnClearLgXs = Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'search-label lbl-clear visible-lg visible-xs']);
$btnClearTab = Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'search-label lbl-clear visible-tab']);
$btnClearMdSm = Html::a('<i class="fa fa-times"></i>', '', ['class' => 'search-label lbl-clear visible-md visible-sm']);

if ($popover):

    PopoverX::begin([
        'size' => PopoverX::SIZE_LARGE,
        'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
        'toggleButton' => ['label' => '<i class="fa fa-search"></i> Search', 'class' => 'btn btn-round btn-default btn-search-toggle'],
        'header' => '&nbsp;',
        'options' => ['id' => 'popover-search', 'class' => 'popover-search'],
    ]); ?>

        <div class="search-box popover-search-box">
    
            <?= Html::beginForm(['page/result-map', 'searchType' => $keywordType, 'city' => 'city_name'], 'get', [
                'class' => 'widget-search-map'
            ]); ?>
    
                <div class="row">
                    <div class="col-sm-12 col">
                        <div class="form-group">
    
                            <?= Html::textInput('nm', $keywordName, [
                                'class' => 'form-control input-name',
                                'placeholder' => 'Ketik Nama Tempat / Jenis Makanan / Alamat'
                            ]) ?>
    
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col">
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
                    
                    <div class="col-sm-6 col">
                        <div class="form-group">
    
                            <?php
                            echo !empty($keywordCoordinate) && !empty($keywordRadius) ? $spanClear : null;
                            echo Html::hiddenInput('cmp', $keywordCoordinate, ['class' => 'coordinate-map']); 
                            echo Html::hiddenInput('rmp', $keywordRadius, ['class' => 'radius-map']); 
                            echo Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]); ?>
    
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col">
                        <div class="form-group">
    
                            <?php
                            echo !empty($keywordProductId) ? $spanClear : null;
                            echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']); 
                            echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>
    
                        </div>
                    </div>
                        
                    <?php
                    if (!empty($keywordType) && ($keywordType == Yii::t('app', 'favorite') || $keywordType == Yii::t('app', 'promo'))): ?>
        
                        <div class="col-sm-6 col">
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
                
                	<?php
                    endif; ?>
                    
                </div>

                <div class="row">
                	
                	<?php
                	if (!empty($keywordType) && ($keywordType == Yii::t('app', 'favorite') || $keywordType == Yii::t('app', 'online-order'))): ?>
                    
                        <div class="col-sm-6 col">
                            <div class="form-group">
                                
        						<?php
        						echo $keywordPriceMin !== null && $keywordPriceMax !== null ? $spanClear : null; 
        						echo Html::hiddenInput('pmn', $keywordPriceMin, ['class' => 'price-min']);
        						echo Html::hiddenInput('pmx', $keywordPriceMax, ['class' => 'price-max']);
        						echo Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>
    
    						</div>
                        </div>
                    
            		<?php
                    endif; ?>
    
                    <div class="col-sm-3 col">
    					
                        <?= $btnSubmitLgXs ?>
                        <?= $btnSubmitTab ?>
                        <?= $btnSubmitMdSm ?>
    
                    </div>
    
                    <div class="col-sm-3 col">
    
                        <?= $btnClearLgXs ?>
                        <?= $btnClearTab ?>
                        <?= $btnClearMdSm ?>
        
                    </div>
                </div>
    
            <?= Html::endForm(); ?>
    
        </div>

    <?php
    PopoverX::end();
    
else: ?>

    <div class="search-box">

        <?= Html::beginForm(['page/result-map', 'searchType' => $keywordType, 'city' => 'city_name'], 'get', [
            'class' => 'widget-search-map'
        ]); ?>

                <div class="row">
                    <div class="col-sm-12 col">
                        <div class="form-group">
    
                            <?= Html::textInput('nm', $keywordName, [
                                'class' => 'form-control input-name',
                                'placeholder' => 'Ketik Nama Tempat / Jenis Makanan / Alamat'
                            ]) ?>
    
                        </div>
                    </div>
                    
                    <div class="col-sm-12 col">
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
                    
                    <div class="col-sm-12 col">
                        <div class="form-group">
    
                            <?php
                            echo !empty($keywordProductId) ? $spanClear : null;
                            echo Html::hiddenInput('pct', $keywordProductId, ['class' => 'product-category-id']); 
                            echo Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]); ?>
    
                        </div>
                    </div>
                        
                    <?php
                    if (!empty($keywordType) && ($keywordType == Yii::t('app', 'favorite') || $keywordType == Yii::t('app', 'promo'))): ?>
        
                        <div class="col-sm-12 col">
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
                
                	<?php
                    endif; ?>
                    
                </div>

                <div class="row">
                	
                	<?php
                	if (!empty($keywordType) && ($keywordType == Yii::t('app', 'favorite') || $keywordType == Yii::t('app', 'online-order'))): ?>
                    
                        <div class="col-sm-12 col mb-20">
                        	<div class="form-group">
                            
    							<?php
    							echo $keywordPriceMin !== null && $keywordPriceMax !== null ? $spanClear : null; 
    							echo Html::hiddenInput('pmn', $keywordPriceMin, ['class' => 'price-min']);
    							echo Html::hiddenInput('pmx', $keywordPriceMax, ['class' => 'price-max']);
    							echo Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>
    						
							</div>
                        </div>
                    
            		<?php
                    endif; ?>
                    
                    <div class="col-sm-12 col">
                        <div class="form-group">
    
                            <?php
                            echo !empty($keywordCoordinate) && !empty($keywordRadius) ? $spanClear : null;
                            echo Html::hiddenInput('cmp', $keywordCoordinate, ['class' => 'coordinate-map']); 
                            echo Html::hiddenInput('rmp', $keywordRadius, ['class' => 'radius-map']); 
                            echo Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]); ?>
    
                        </div>
                    </div>
    
    				<div class="form-group">
                        <div class="col-sm-12 col">
        					
                            <?= $btnSubmitLgXs ?>
                            <?= $btnSubmitTab ?>
                            <?= $btnSubmitMdSm ?>
        
                        </div>
        
                        <div class="col-sm-12 col">
        
                            <?= $btnClearLgXs ?>
                            <?= $btnClearTab ?>
                            <?= $btnClearMdSm ?>
            
                        </div>
                    </div>
                </div>

        <?= Html::endForm(); ?>

    </div>

<?php
endif; ?>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.css', ['depends' => 'yii\web\YiiAsset']);

$csscript = '
    
    .modal {
        z-index: 1051;
    }
';

$this->registerCss($csscript);

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

    $("#popover-search").on("shown.bs.modal", function (event) {

        $(this).css("z-index", 1039);
    });

    $(".widget-search-map").on("submit", function() {

        var action = $(this).attr("action").replace("city_name", $(this).find(".city-id").find(":selected")[0].label.toLowerCase().replace(" ", "-"));

        $(this).attr("action", action);
    });

    $(".lbl-clear").on("click", function() {

        $(".input-name, .product-category-id, .coordinate-map, .radius-map, .price-min, .price-max").val("");
        $(".category-id").val(null).trigger("change");

        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".search-field-box-clear").remove();

        return false;
    });
';

$this->registerJs($jscript); ?>