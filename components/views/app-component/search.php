<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $showFacilityFilter bool */
/* @var $type string */

$keywordType = $keyword['searchType'];

if (!empty($type) && $type == 'result-map-page'):

    echo Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-map-toggle', 'data-keyword' => $keywordType]);
else: ?>

	<div class="search-box <?= !empty($type) ? $type : "" ?>">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'favorite') ? 'active' : '' ?>">
                <a href="#favorite" aria-controls="favorite" role="tab" data-toggle="tab" id="favorite-id" class="favorite-class"><strong><?= \Yii::t('app', 'Favorite') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'promo') ? 'active' : '' ?>">
                <a href="#special" aria-controls="special" role="tab" data-toggle="tab" id="special-id" class="special-class"><strong><?= \Yii::t('app', 'Promo') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'online-order') ? 'active' : '' ?>">
                <a href="#order" aria-controls="order" role="tab" data-toggle="tab" id="order-id" class="order-class"><strong><?= \Yii::t('app', 'Online Order') ?></strong></a>
            </li>
        </ul>

        	<?= Html::beginForm(['page/result-list'], 'get', [
                'class' => 'search-favorite-class'
            ]) ?>

    	    	<div class="form-group">
                    <div class="input-group">
                    	<div class="input-group-addon">
                    		<i class="fa fa-search"></i>
                    	</div>

                	    	<?php
                	    	echo Html::hiddenInput('searchType', \Yii::t('app', 'favorit'), ['class' => 'search-type']);
                	    	echo Html::hiddenInput('city', strtolower($keyword['cityName']));
                	    	echo Html::textInput('nm', $keyword['name'], ['class' => 'form-control search-input', 'placeholder' => 'Mau cari apa di Asikmakan?']);
                	    	echo Html::hiddenInput('cty', $keyword['city'], ['class' => 'city']);
                	    	echo Html::hiddenInput('pct', $keyword['product']['id'], ['class' => 'product-category']);
                	    	echo Html::hiddenInput('ctg', $keyword['category'], ['class' => 'category']);
                	    	echo Html::hiddenInput('pmn', $keyword['price']['min'], ['class' => 'price-min']);
                	    	echo Html::hiddenInput('pmx', $keyword['price']['max'], ['class' => 'price-max']);
                	    	echo Html::hiddenInput('cmp', $keyword['map']['coordinate'], ['class' => 'coordinate-map']);
                	    	echo Html::hiddenInput('rmp', $keyword['map']['radius'], ['class' => 'radius-map']); ?>

					</div>
					<div class="row mt-20">
                        <div class="btn-widget-search">
                        	<div class="col-sm-2 col-sm-offset-6 col-xs-2 p-5">
                                <div class="form-group">
                                    <?= Html::Button('<i class="fa fa-filter"></i><span class="btn-filter hidden-xs">Filter</span>', ['class' => 'btn btn-block btn-round btn-d btn-search filter-input']) ?>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-5 p-5">
                                <div class="form-group">
                                    <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search']) ?>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-5 p-5">
                                <div class="form-group">
                                    <?= Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'btn btn-block btn-default search-label lbl-clear']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>

    		<?= Html::endForm() ?>
	</div>

<?php
endif; ?>