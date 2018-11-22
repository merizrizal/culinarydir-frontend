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
$btnClearMdSm = Html::a('<i class="fa fa-times"></i>', '', ['class' => 'search-label lbl-clear visible-md visible-sm']); ?>

<div class="search-box">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?= empty($keyword['special']) ? 'active' : '' ?>">
            <a href="#favorite<?= !empty($id) ? '-' . $id : '' ?>" aria-controls="favorite" role="tab" data-toggle="tab"><?= Yii::t('app', 'Find Favourite Foods') ?></a>
        </li>
        <li role="presentation" class="<?= !empty($keyword['special']) ? 'active' : '' ?>">
            <a href="#special<?= !empty($id) ? '-' . $id : '' ?>" aria-controls="special" role="tab" data-toggle="tab"><?= Yii::t('app', 'Find Specials & Discounts') ?></a>
        </li>
    </ul>

    <!-- Tab Favorite -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade <?= empty($keyword['special']) ? 'in active' : '' ?>" id="favorite<?= !empty($id) ? '-' . $id : '' ?>">

            <?= Html::beginForm(['page/result-list'], 'get', [
                'id' => 'search-favorite'
            ]) ?>

                <?= Html::hiddenInput('special', 0) ?>

                <div class="row">
                    <div class="col-sm-10 col-xs-12 col">
                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-9 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::textInput('name', !empty($keyword['name']) ? $keyword['name'] : null, [
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
                                    if (!empty($keyword['product']['id'])):

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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                                    <?= Html::hiddenInput('coordinate_map', $coordinate, ['class' => 'coordinate-map'])?>
                                    <?= Html::hiddenInput('radius_map', $radius, ['class' => 'radius-map'])?>
                                    <?= Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

                                </div>
                            </div>
                        </div>

                        <?php
                        if ($showFacilityFilter):
                        
                            $modelFacility = Facility::find()
                                ->orderBy('name')
                                ->asArray()->all();

                            $colDivider = ceil(count($modelFacility) / 3);

                            $column = '<div class="col-lg-4 col-md-4 col-sm-4 col-tab-4 col-xs-12 col">'; ?>

                            <div class="row mb-10">
                                <div class="col-lg-12 col-xs-12 col">
                                    <a class="search-label more-option" data-toggle="collapse" href=".facility-collapse">More Options
                                        <span class="fa fa-chevron-circle-down"></span>
                                    </a>
                                    <div class="facility-collapse collapse <?= !empty($keyword['facility']) ? 'in' : '' ?>">
                                    	<div class="form-group">
                                        	<div class="row mt-10">                                            

                                                <?= Html::checkboxList('facility_id', !empty($keyword['facility']) ? $keyword['facility'] : null,
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
                                                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 col-tab-12 col">
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

        <div role="tabpanel" class="tab-pane fade <?= !empty($keyword['special']) ? 'in active' : '' ?>" id="special<?= !empty($id) ? '-' . $id : '' ?>">

            <?= Html::beginForm(['page/result-list'], 'get', [
                'id' => 'search-special'
            ]) ?>

                <?= Html::hiddenInput('special', 1) ?>

                <div class="row">
                    <div class="col-sm-10 col-xs-12 col">
                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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
                                            'style' => 'width: 100%'
                                        ]) ?>

                                </div>
                            </div>

                            <div class="col-sm-9 col-tab-6 col-xs-12 col">
                                <div class="form-group">

                                    <?= Html::textInput('name', !empty($keyword['name']) ? $keyword['name'] : null, [
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
                                    if (!empty($keyword['product']['id'])):

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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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

                                    <?= Html::hiddenInput('coordinate_map', $coordinate, ['class' => 'coordinate-map'])?>
                                    <?= Html::hiddenInput('radius_map', $radius, ['class' => 'radius-map'])?>
                                    <?= Html::a($radiusLabel, '', ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

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

    $(".city-id").val("1").trigger("change");

    $(".lbl-clear").on("click", function() {

        $(".input-name, .product-category-id, .coordinate-map, .radius-map, .price-min, .price-max").val("");
        $(".category-id").val(null).trigger("change");
        $(".facility").iCheck("uncheck");

        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".search-field-box-clear").remove();

        return false;
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>