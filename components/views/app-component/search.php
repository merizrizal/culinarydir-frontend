<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $showFacilityFilter bool */
/* @var $type string */
// print_r($keyword);exit;
$keywordType = $keyword['searchType'];
$btnFilter = Html::Button('<i class="fa fa-filter"></i> Filter', ['class' => 'btn btn-block btn-round btn-d btn-search filter-input']);
$btnSearch = Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search']);
$inputSearch = Html::textInput('nm', $keyword['name'], ['class' => 'form-control search-input', 'placeholder' => 'Mau cari apa di Asikmakan?']);

if (!empty($type) && $type == 'result-map-page'):

    echo Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-map-toggle', 'data-keyword' => $keywordType]);
else: ?>

	<div class="search-box <?= !empty($type) ? $type : "" ?>">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'favorite') ? 'active' : '' ?>">
                <a href="#favorite1" aria-controls="favorite" role="tab" data-toggle="tab"><strong><?= \Yii::t('app', 'Favorite') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'promo') ? 'active' : '' ?>">
                <a href="#special1" aria-controls="special" role="tab" data-toggle="tab"><strong><?= \Yii::t('app', 'Promo') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'online-order') ? 'active' : '' ?>">
                <a href="#order1" aria-controls="order" role="tab" data-toggle="tab"><strong><?= \Yii::t('app', 'Online Order') ?></strong></a>
            </li>
        </ul>
		<div class="tab-content">
    		<div role="tabpanel" class="tab-pane <?= $keywordType == \Yii::t('app', 'favorite') ? 'in active' : '' ?>" id="favorite1">

            	<?= Html::beginForm(['page/result-list', 'searchType' => \Yii::t('app', 'favorite'), 'city' => 'bandung',
            	    'cty' => $keyword['city'],
            	    'pct' => !empty($keyword['product']['id']) ? $keyword['product']['id'] : '',
            	    'ctg' => !empty($keyword['category']) ? $keyword['category'] : '',
            	    'pmn' => !empty($keyword['price']['min']) ? $keyword['price']['min'] : '',
            	    'pmx' => !empty($keyword['price']['max']) ? $keyword['price']['max'] : '',
            	    'cmp' => !empty($keyword['map']['coordinate']) ? $keyword['map']['coordinate'] : '',
            	    'rmp' => !empty($keyword['map']['radius']) ? $keyword['map']['radius'] : ''], 'get', [
                        'class' => 'search-favorite'
                ]) ?>

                	<div class="form-group">
                        <div class="input-group">
                        	<div class="input-group-addon">
                        		<i class="fa fa-search"></i>
                        	</div>
                        	<?= $inputSearch ?>
                    	</div>
                    	<div class="row mt-20">
                        	<div class="col-sm-2 col-sm-offset-8 col-xs-5">
                                <div class="form-group">
                                    <?= $btnFilter ?>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-5">
                                <div class="form-group">
                                    <?= $btnSearch ?>
                                </div>
                            </div>
                        </div>
                	</div>

        		<?= Html::endForm(); ?>

    		</div>
    		<div role="tabpanel" class="tab-pane <?= $keywordType == \Yii::t('app', 'promo') ? 'in active' : '' ?>" id="special1">

    			<?= Html::beginForm(['page/result-list', 'searchType' => \Yii::t('app', 'promo'), 'city' => 'bandung',
            	    'cty' => $keyword['city'],
            	    'pct' => !empty($keyword['product']['id']) ? $keyword['product']['id'] : '',
    			    'ctg' => !empty($keyword['category']) ? $keyword['category'] : '',
            	    'cmp' => !empty($keyword['map']['coordinate']) ? $keyword['map']['coordinate'] : '',
            	    'rmp' => !empty($keyword['map']['radius']) ? $keyword['map']['radius'] : ''], 'get', [
                        'class' => 'search-special'
                ]) ?>

        			<div class="form-group">
                        <div class="input-group">
                        	<div class="input-group-addon">
                        		<i class="fa fa-search"></i>
                        	</div>
                        	<?= $inputSearch ?>
                    	</div>
                    	<div class="row mt-20">
                        	<div class="col-sm-2 col-sm-offset-8 col-xs-5">
                                <div class="form-group">
                                    <?= $btnFilter ?>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-5">
                                <div class="form-group">
                                    <?= $btnSearch ?>
                                </div>
                            </div>
                        </div>
                	</div>

            	<?= Html::endForm(); ?>

    		</div>
    	</div>
	</div>

<?php
endif;

$jscript = '
    $(".search-favorite, .search-special, .search-order").on("submit", function() {

        var action = $(this).attr("action").replace("city_name", $(this).find(".city-id").find(":selected")[0].label.toLowerCase().replace(" ", "-"));

        $(this).attr("action", action);
    });


';

$this->registerJs($jscript); ?>