<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\popover\PopoverX;
use core\models\City;
use core\models\Category;

/* @var $this yii\web\View */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

if ($popover):

    PopoverX::begin([
    'size' => PopoverX::SIZE_LARGE,
    'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
    'toggleButton' => ['label' => '<i class="fa fa-search"></i> Search', 'class' => 'btn btn-round btn-default btn-search-toggle'],
    'headerOptions' => ['style' => 'display:none'],
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
                            'placeholder' => 'Ketik Nama Tempat / Jenis Makanan'
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
                        $valueCoordinate = null;
                        $valueRadius = null;
                        $valueRadiusLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Region') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                        $styleRadiusLabel = null;

                        if (!empty($keyword['coordinate']) && !empty($keyword['radius'])):

                            $valueCoordinate = $keyword['coordinate'];
                            $valueRadius = $keyword['radius'];
                            $valueRadiusLabel = '<span class="search-field-box-placeholder">' . Yii::$app->formatter->asShortLength($keyword['radius']) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                            $styleRadiusLabel = 'color: #555555;'; ?>

                            <span class="search-field-box-clear">×</span>

                        <?php
                        endif; ?>

                        <?= Html::hiddenInput('coordinate_map', $valueCoordinate, ['class' => 'coordinate-map']) ?>
                        <?= Html::hiddenInput('radius_map', $valueRadius, ['class' => 'radius-map']) ?>
                        <?= Html::a($valueRadiusLabel, null, ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-tab-6 col-xs-12 col">
                    <div class="form-group">

                        <?php
                        $valueProductId = null;
                        $valueProductLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Product Category') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                        $styleProductLabel = null;

                        if (!empty($keyword['product'])):

                            $valueProductId = $keyword['product']['id'];
                            $valueProductLabel = '<span class="search-field-box-placeholder">' . $keyword['product']['name'] . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                            $styleProductLabel = 'color: #555555;'; ?>

                            <span class="search-field-box-clear">×</span>

                        <?php
                        endif; ?>

                        <?= Html::hiddenInput('product_category', $valueProductId, ['class' => 'product-category-id']) ?>
                        <?= Html::a($valueProductLabel, null, ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>

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
                <div class="col-sm-6 col-tab-6 col-xs-12 col">
                    <div class="form-group">

                        <?php
                        if (empty($keyword['special'])):

                            $valuePriceMin = !empty($keyword['price_min']) ? $keyword['price_min'] : 0;
                            $valuePriceMax = !empty($keyword['price_max']) ? $keyword['price_max'] : 0;
                            $valuePriceLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Price') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                            $stylePriceLabel = null;

                            if (!empty($keyword['price_min']) || !empty($keyword['price_max'])):

                                $valuePriceMinLabel = ($valuePriceMin == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($valuePriceMin);
                                $valuePriceMaxLabel = ($valuePriceMax == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($valuePriceMax);
                                $valuePriceLabel = '<span class="search-field-box-placeholder">' . $valuePriceMinLabel . ' - ' . $valuePriceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                $stylePriceLabel = 'color: #555555;'; ?>

                                <span class="search-field-box-clear">×</span>

                            <?php
                            endif; ?>

                            <?= Html::hiddenInput('price_min', $valuePriceMin, ['class' => 'price-min'])?>
                            <?= Html::hiddenInput('price_max', $valuePriceMax, ['class' => 'price-max'])?>
                            <?= Html::a($valuePriceLabel, null, ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

                        <?php
                        endif; ?>

                    </div>
                </div>

                <div class="col-sm-3 col-tab-3 col-xs-12 col">

                    <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']) ?>
                    <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-tab']) ?>
                    <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']) ?>

                </div>

                <div class="col-sm-3 col-tab-3 col-xs-12 col">

                    <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn lbl-clear visible-lg visible-xs']) ?>
                    <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn lbl-clear visible-tab']) ?>
                    <?= Html::a('<i class="fa fa-times"></i>', null, ['class' => 'btn lbl-clear visible-md visible-sm']) ?>

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
                            'placeholder' => 'Ketik Nama Tempat / Jenis Makanan'
                        ]) ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-tab-6 col-xs-12 col">
                    <div class="form-group">

                        <?php
                        $valueProductId = null;
                        $valueProductLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Product Category') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                        $styleProductLabel = null;

                        if (!empty($keyword['product'])):

                            $valueProductId = $keyword['product']['id'];
                            $valueProductLabel = '<span class="search-field-box-placeholder">' . $keyword['product']['name'] . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                            $styleProductLabel = 'color: #555555;'; ?>

                            <span class="search-field-box-clear">×</span>

                        <?php
                        endif; ?>

                        <?= Html::hiddenInput('product_category', $valueProductId, ['class' => 'product-category-id']) ?>
                        <?= Html::a($valueProductLabel, null, ['class' => 'form-control search-field-box btn-product-category', 'style' => $styleProductLabel]) ?>

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
                                $valuePriceMin = !empty($keyword['price_min']) ? $keyword['price_min'] : 0;
                                $valuePriceMax = !empty($keyword['price_max']) ? $keyword['price_max'] : 0;
                                $valuePriceLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Price') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                $stylePriceLabel = null;

                                if (!empty($keyword['price_min']) || !empty($keyword['price_max'])):

                                    $valuePriceMinLabel = ($valuePriceMin == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($valuePriceMin);
                                    $valuePriceMaxLabel = ($valuePriceMax == 0) ? 'Any' : Yii::$app->formatter->asNoSymbolCurrency($valuePriceMax);
                                    $valuePriceLabel = '<span class="search-field-box-placeholder">' . $valuePriceMinLabel . ' - ' . $valuePriceMaxLabel . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                                    $stylePriceLabel = 'color: #555555;'; ?>

                                    <span class="search-field-box-clear">×</span>

                                <?php
                                endif; ?>

                                <?= Html::hiddenInput('price_min', $valuePriceMin, ['class' => 'price-min'])?>
                                <?= Html::hiddenInput('price_max', $valuePriceMax, ['class' => 'price-max'])?>
                                <?= Html::a($valuePriceLabel, null, ['class' => 'form-control search-field-box btn-price', 'style' => $stylePriceLabel]) ?>

                        </div>
                    </div>

                <?php
                endif; ?>

                <div class="col-tab-6 col-xs-12 col">
                    <div class="form-group">

                        <?php
                        $valueCoordinate = null;
                        $valueRadius = null;
                        $valueRadiusLabel = '<span class="search-field-box-placeholder">' . Yii::t('app', 'Region') . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                        $styleRadiusLabel = null;

                        if (!empty($keyword['coordinate']) && !empty($keyword['radius'])):

                            $valueCoordinate = $keyword['coordinate'];
                            $valueRadius = $keyword['radius'];
                            $valueRadiusLabel = '<span class="search-field-box-placeholder">' . Yii::$app->formatter->asShortLength($keyword['radius']) . '</span><span class="search-field-box-arrow"><i class="fa fa-caret-right"></i></span>';
                            $styleRadiusLabel = 'color: #555555;'; ?>

                            <span class="search-field-box-clear">×</span>

                        <?php
                        endif; ?>

                        <?= Html::hiddenInput('coordinate_map', $valueCoordinate, ['class' => 'coordinate-map']) ?>
                        <?= Html::hiddenInput('radius_map', $valueRadius, ['class' => 'radius-map']) ?>
                        <?= Html::a($valueRadiusLabel, null, ['class' => 'form-control search-field-box btn-region', 'style' => $styleRadiusLabel]) ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col">

                    <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-lg visible-xs']) ?>
                    <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search visible-tab']) ?>
                    <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-block btn-round btn-d btn-search visible-md visible-sm']) ?>

                </div>

                <div class="col-xs-12 col">

                    <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn lbl-clear visible-lg visible-xs']) ?>
                    <?= Html::a('<i class="fa fa-times"></i> Clear', null, ['class' => 'btn lbl-clear visible-tab']) ?>
                    <?= Html::a('<i class="fa fa-times"></i>', null, ['class' => 'btn lbl-clear visible-md visible-sm']) ?>

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

    $(".city-id").val("1").trigger("change");

    $(".category-id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
        minimumResultsForSearch: -1,
        allowClear: true
    });

    $("#popover-search").on("shown.bs.modal", function (event) {

        $(this).css("z-index", 1039);
    });

    $(".lbl-clear").on("click", function() {

        $(".input-name").val("");
        $(".product-category-id").val("");
        $(".category-id").val(null).trigger("change");
        $(".coordinate-map").val("");
        $(".radius-map").val("");
        $(".price-min, .price-max").val("");

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

$this->registerJs($jscript); ?>
