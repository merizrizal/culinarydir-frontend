<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use core\models\City;
use core\models\Category;

/* @var $this yii\web\View */

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
$styleRadiusLabel = null; ?>

<div class="search-box">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?= empty($keyword['special']) ? 'active' : '' ?>">
            <a href="#favorite<?= !empty($id) ? '-' . $id : ''?>" aria-controls="favorite" role="tab" data-toggle="tab">Cari Makanan Favorit</a>
        </li>
        <li role="presentation" class="<?= !empty($keyword['special']) ? 'active' : '' ?>">
            <a href="#special<?= !empty($id) ? '-' . $id : ''?>" aria-controls="special" role="tab" data-toggle="tab">Cari Spesial &amp; Diskon</a>
        </li>
    </ul>

    <!-- Tab Favorite -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade <?= empty($keyword['special']) ? 'in active' : '' ?>" id="favorite<?= !empty($id) ? '-' . $id : ''?>">

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
                                        'placeholder' => 'Ketik Nama Tempat / Jenis Makanan'
                                    ]) ?>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-tab-6 col-xs-12 col">
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
                                    <?= Html::a($productLabel, null, ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>

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
                                    <?= Html::a($priceLabel, null, ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

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
                                    <?= Html::a($radiusLabel, null, ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

                                </div>
                            </div>
                        </div>

                        <?php
                        if (!empty($showFacilityFilter)):

                            $colDivider = ceil(count($modelFacility) / 3);

                            $column = '<div class="col-lg-4 col-md-4 col-sm-4 col-tab-4 col-xs-12 col">'; ?>

                            <div class="row mb-10">
                                <div class="col-lg-12 col-xs-12 col">
                                    <a class="search-label" data-toggle="collapse" href=".facility-collapse">More Options
                                        <span class="fa fa-chevron-circle-down"></span>
                                    </a>
                                    <div class="<?= !empty($keyword['facility']) ? 'collapse in facility-collapse' : 'collapse facility-collapse' ?>">
                                        <div class="row mt-10">
                                            <div class="form-group">

                                                <?= Html::checkboxList('facility_id', !empty($keyword['facility']) ? $keyword['facility'] : null,
                                                    ArrayHelper::map(
                                                        $modelFacility,
                                                        'id',
                                                        function($data) {
                                                            return $data['name'];
                                                        }
                                                    ),
                                                    [
                                                        'item' => function ($index, $label, $name, $checked, $value) use($colDivider, $column, $modelFacility) {

                                                            $checkboxes =  '<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 col-tab-12 col">' .
                                                                                '<label>' .
                                                                                    Html::checkbox($name, $checked, [
                                                                                        'value' => $value,
                                                                                        'class' => 'facility icheck',
                                                                                    ]) . ' ' . $label .
                                                                                '</label>' .
                                                                            '</div>';

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

                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']) ?>
                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-tab']) ?>
                            <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']) ?>

                        </div>
                        <div class="btn-clear-container">

                            <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn search-label lbl-clear visible-lg visible-xs']) ?>
                            <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn search-label lbl-clear visible-tab']) ?>
                            <?= Html::a('<i class="fa fa-times"></i>', null, ['class' => 'btn search-label lbl-clear visible-md visible-sm']) ?>

                        </div>
                    </div>
                </div>

            <?= Html::endForm() ?>

        </div>

        <div role="tabpanel" class="tab-pane fade <?= !empty($keyword['special']) ? 'in active' : '' ?>" id="special<?= !empty($id) ? '-' . $id : ''?>">

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
                                        'placeholder' => 'Ketik Nama Tempat / Jenis Makanan'
                                    ]) ?>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-sm-offset-3 col-tab-6 col-xs-12 col">
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
                                    <?= Html::a($productLabel, null, ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>

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
                                    <?= Html::a($radiusLabel, null, ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 col-xs-12 col">
                        <div class="form-group">

                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['id' => 'submit-special', 'class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']) ?>
                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['id' => 'submit-special', 'class' => 'btn btn-block btn-round btn-d btn-search visible-tab']) ?>
                            <?= Html::submitButton('<i class="fa fa-search"></i>', ['id' => 'submit-special', 'class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']) ?>

                        </div>
                        <div class="btn-clear-container">

                            <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn search-label lbl-clear visible-lg visible-xs']) ?>
                            <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn search-label lbl-clear visible-tab']) ?>
                            <?= Html::a('<i class="fa fa-times"></i>', null, ['class' => 'btn search-label lbl-clear visible-md visible-sm']) ?>

                        </div>
                    </div>
                </div>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$csscript = '
    .search-label {
        color:#444 !important;
    }

    .search-label:hover {
        color:#222 !important;
    }

    .select2-results__option {
        padding: 2px 6px;
    }

    .select2-container--krajee .select2-results > .select2-results__options {
        max-height: 210px;
    }
';

$this->registerCss($csscript);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".city-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City') . '",
        minimumResultsForSearch: -1
    });

    $(".city-id").val("1").trigger("change");

    $(".category-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $(".lbl-clear").on("click", function() {

        $(".input-name").val("");
        $(".product-category-id").val("");
        $(".category-id").val(null).trigger("change");
        $(".coordinate-map").val("");
        $(".radius-map").val("");
        $(".price-min, .price-max").val("");
        $(".facility").iCheck("uncheck");

        $(".price-min-select").val(null).trigger("change");
        $(".price-max-select").val(null).trigger("change");

        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-radius-500").addClass("active");
        $(".btn-radius-500").siblings().removeClass("active");
        $(".search-field-box-clear").remove();

        initMap();
        return false;
    });

    $(".btn-product-category").parent().find(".search-field-box-clear").on("click", function() {

        $(".product-category-id").val("");
        $(".btn-product-category").html("' . Yii::t('app', 'Product Category') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(this).remove();
    });

    $(".btn-price").parent().find(".search-field-box-clear").on("click", function() {

        $(".price-min-select").val(null).trigger("change");
        $(".price-max-select").val(null).trigger("change");

        $(".btn-price").siblings(".price-min, .price-max").val("");
        $(".btn-price").html("' . Yii::t('app', 'Price') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(this).remove();
    });

    $(".btn-region").parent().find(".search-field-box-clear").on("click", function() {

        $(".coordinate-map").val("");
        $(".radius-map").val("");
        $(".btn-region").html("' . Yii::t('app', 'Region') . ' <span class=\"search-field-box-arrow\"><i class=\"fa fa-caret-right\"></i></span>").css("color", "#aaa");
        $(".btn-radius-500").trigger("click");
        $(this).remove();
    });
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>