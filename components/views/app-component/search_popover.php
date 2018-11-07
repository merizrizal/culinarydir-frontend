<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\popover\PopoverX;
use core\models\City;
use core\models\Category;

/* @var $this yii\web\View */
/* @var $keyword yii\web\View */
/* @var $popover bool */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$productId = null;
$productLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Product Category') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
$styleProductLabel = null;

$priceMin = !empty($keyword['price_min']) ? $keyword['price_min'] : 0;
$priceMax = !empty($keyword['price_max']) ? $keyword['price_max'] : 0;
$priceLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Price') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
$stylePriceLabel = null;

$coordinate = null;
$radius = null;
$radiusLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Region') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
$styleRadiusLabel = null;

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
    
            <?= Html::beginForm(['page/result-map'], 'get', [
                'id' => 'widget-search-map'
            ]); ?>
    
                <?php
                if (empty($keyword['special'])) {
        
                    echo Html::hiddenInput('special', 0);
                } else if (!empty($keyword['special'])) {
        
                    echo Html::hiddenInput('special', 1);
                } ?>
    
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col">
                            <div class="form-group">
        
                                <?= Html::textInput('name', !empty($keyword['name']) ? $keyword['name'] : null, [
                                    'class' => 'form-control input-name',
                                    'placeholder' => 'Ketik Nama Tempat / Jenis Makanan / Alamat'
                                ]) ?>
        
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-sm-6 col-tab-6 col-xs-12 col">
                            <div class="form-group">
        
                                <?= Html::dropDownList('city_id', !empty($keyword['city']) ? $keyword['city'] : null,
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
        
                        <div class="col-sm-6 col-tab-6 col-xs-12 col">
                            <div class="form-group">
        
                                <?php
                                if (!empty($keyword['coordinate']) && !empty($keyword['radius'])):
        
                                    $coordinate = $keyword['coordinate'];
                                    $radius = $keyword['radius'];
                                    $radiusLabel = '<span class="search-field-box-placeholder">' . Yii::$app->formatter->asShortLength($keyword['radius']) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                    $styleRadiusLabel = 'color: #555555;'; ?>
        
                                    <span class="search-field-box-clear">×</span>
        
                                <?php
                                endif; ?>
        
                                <?= Html::hiddenInput('coordinate_map', $coordinate, ['class' => 'coordinate-map']) ?>
                                <?= Html::hiddenInput('radius_map', $radius, ['class' => 'radius-map']) ?>
                                <?= Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>
        
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-sm-6 col-tab-6 col-xs-12 col">
                            <div class="form-group">
        
                                <?php
                                if (!empty($keyword['product'])):
        
                                    $productId = $keyword['product']['id'];
                                    $productLabel = '<span class="search-field-box-placeholder">' . $keyword['product']['name'] . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                    $styleProductLabel = 'color: #555555;'; ?>
        
                                    <span class="search-field-box-clear">×</span>
        
                                <?php
                                endif; ?>
        
                                <?= Html::hiddenInput('product_category', $productId, ['class' => 'product-category-id']) ?>
                                <?= Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>
        
                            </div>
                        </div>
        
                        <div class="col-sm-6 col-tab-6 col-xs-12 col">
                            <div class="form-group">
        
                                <?= Html::dropDownList('category_id', !empty($keyword['category']) ? $keyword['category'] : null,
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
                    
                    	<?php
                        if (empty($keyword['special'])): ?>
                        
                            <div class="col-sm-6 col-tab-6 col-xs-12 col">
                                <div class="form-group">
                                
        							<?php
                                    if (!empty($keyword['price_min']) || !empty($keyword['price_max'])):
        
                                        $priceMinLabel = ($priceMin == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($priceMin);
                                        $priceMaxLabel = ($priceMax == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($priceMax);
                                        $priceLabel = '<span class="search-field-box-placeholder">' . $priceMinLabel . ' - ' . $priceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                        $stylePriceLabel = 'color: #555555;'; ?>
        
                                        <span class="search-field-box-clear">×</span>
        
                                    <?php
                                    endif; ?>
        
                                    <?= Html::hiddenInput('price_min', $priceMin, ['class' => 'price-min'])?>
                                    <?= Html::hiddenInput('price_max', $priceMax, ['class' => 'price-max'])?>
                                    <?= Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>
        
                                </div>
                            </div>
                            
                        <?php
                        endif; ?>
        
                        <div class="col-sm-3 col-tab-3 col-xs-12 col">
        
                            <?= $btnSubmitLgXs ?>
                            <?= $btnSubmitTab ?>
                            <?= $btnSubmitMdSm ?>
        
                        </div>
        
                        <div class="col-sm-3 col-tab-3 col-xs-12 col">
        
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

        <?= Html::beginForm(['page/result-map'], 'get', [
            'id' => 'widget-search-map'
        ]); ?>

            <?php
            if (empty($keyword['special'])) {
    
                echo Html::hiddenInput('special', 0);    
            } else if (!empty($keyword['special'])) {
    
                echo Html::hiddenInput('special', 1);    
            } ?>

                <div class="row">
                    <div class="col-tab-6 col-xs-12 col">
                        <div class="form-group">
    
                            <?= Html::dropDownList('city_id', !empty($keyword['city']) ? $keyword['city'] : null,
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
    
                    <div class="col-tab-6 col-xs-12 col">
                        <div class="form-group">
    
                            <?= Html::textInput('name', !empty($keyword['name']) ? $keyword['name'] : null, [
                                'class' => 'form-control input-name',
                                'placeholder' => 'Ketik Nama Tempat / Jenis Makanan / Alamat'
                            ]) ?>
    
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-tab-6 col-xs-12 col">
                        <div class="form-group">
    
                            <?php
                            if (!empty($keyword['product'])):
    
                                $productId = $keyword['product']['id'];
                                $productLabel = '<span class="search-field-box-placeholder">' . $keyword['product']['name'] . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                $styleProductLabel = 'color: #555555;'; ?>
    
                                <span class="search-field-box-clear">×</span>
    
                            <?php
                            endif; ?>
    
                            <?= Html::hiddenInput('product_category', $productId, ['class' => 'product-category-id']) ?>
                            <?= Html::a($productLabel, '', ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>
    
                        </div>
                    </div>
    
                    <div class="col-tab-6 col-xs-12 col">
                        <div class="form-group">
    
                            <?= Html::dropDownList('category_id', !empty($keyword['category']) ? $keyword['category'] : null,
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
    
                    <?php
                    if (empty($keyword['special'])): ?>
    
                        <div class="col-tab-6 col-xs-12 col">
                            <div class="form-group">
    
                                    <?php
                                    if (!empty($keyword['price_min']) || !empty($keyword['price_max'])):
    
                                        $priceMinLabel = ($priceMin == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($priceMin);
                                        $priceMaxLabel = ($priceMax == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($priceMax);
                                        $priceLabel = '<span class="search-field-box-placeholder">' . $priceMinLabel . ' - ' . $priceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                        $stylePriceLabel = 'color: #555555;'; ?>
    
                                        <span class="search-field-box-clear">×</span>
    
                                    <?php
                                    endif; ?>
    
                                    <?= Html::hiddenInput('price_min', $priceMin, ['class' => 'price-min'])?>
                                    <?= Html::hiddenInput('price_max', $priceMax, ['class' => 'price-max'])?>
                                    <?= Html::a($priceLabel, '', ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>
    
                            </div>
                        </div>
    
                    <?php
                    endif; ?>
    
                    <div class="col-tab-6 col-xs-12 col">
                        <div class="form-group">
    
                            <?php
                            if (!empty($keyword['coordinate']) && !empty($keyword['radius'])):
    
                                $coordinate = $keyword['coordinate'];
                                $radius = $keyword['radius'];
                                $radiusLabel = '<span class="search-field-box-placeholder">' . Yii::$app->formatter->asShortLength($keyword['radius']) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                $styleRadiusLabel = 'color: #555555;'; ?>
    
                                <span class="search-field-box-clear">×</span>
    
                            <?php
                            endif; ?>
    
                            <?= Html::hiddenInput('coordinate_map', $coordinate, ['class' => 'coordinate-map']) ?>
                            <?= Html::hiddenInput('radius_map', $radius, ['class' => 'radius-map']) ?>
                            <?= Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>
    
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col">
    
                        <?= $btnSubmitLgXs ?>
                        <?= $btnSubmitTab ?>
                        <?= $btnSubmitMdSm ?>
    
                    </div>
    
                    <div class="col-xs-12 col">
    
                        <?= $btnClearLgXs ?>
                        <?= $btnClearTab ?>
                        <?= $btnClearMdSm ?>
    
                    </div>
                </div>

        <?= Html::endForm(); ?>

    </div>

<?php
endif; ?>

<?php
$csscript = '
    .search-box.popover-search-box {
        box-shadow:0 0 0 #000;
        padding: 0;
        margin-top: 15px;
    }

    .btn.lbl-clear {
        color:#444 !important;
        padding: 7px 12px;
    }

    .lbl-clear:hover {
        color:#222 !important;
    }

    .modal {
        z-index: 1051;
    }

    .search-box.popover-search-box .search-field-box-clear {
        top: 4px !important;
    }
';

$this->registerCss($csscript);

$jscript = '
    $(".city-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City') . '",
        minimumResultsForSearch: -1
    });

    $(".category-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $("#popover-search").on("shown.bs.modal", function (event) {

        $(this).css("z-index", 1039);
    });

    $(".city-id").val("1").trigger("change");

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
