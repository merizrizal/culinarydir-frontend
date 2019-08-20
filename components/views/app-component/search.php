<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $showFacilityFilter bool */
/* @var $type string */

$keywordType = $keyword['searchType'];

$widgetSearchInput ='
    <div class="form-group">
        <div class="input-group">
        	<div class="input-group-addon">
        		<i class="fa fa-search"></i>
        	</div>
        	' . Html::textInput('nm', $keyword['name'], ['class' => 'form-control search-input', 'placeholder' => 'Mau cari apa di Asikmakan?']) . '
    	</div>
    	<div class="row mt-20">
            <div class="btn-widget-search">
            	<div class="col-sm-2 col-sm-offset-6 col-xs-4 p-5">
                    <div class="form-group">
                        ' . Html::Button('<i class="fa fa-filter"></i> Filter', ['class' => 'btn btn-block btn-round btn-d btn-search filter-input']) . '
                    </div>
                </div>
                <div class="col-sm-2 col-xs-4 p-5">
                    <div class="form-group">
                        ' . Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-block btn-round btn-d btn-search']) . '
                    </div>
                </div>
                <div class="col-sm-2 col-xs-4 p-5">
                    <div class="form-group">
                        ' . Html::a('<i class="fa fa-times"></i> Clear', '', ['class' => 'btn btn-block btn-default search-label lbl-clear']) . '
                    </div>
                </div>
            </div>
        </div>
	</div>'
;

if (!empty($type) && $type == 'result-map-page'):

    echo Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-map-toggle', 'data-keyword' => $keywordType]);
else: ?>

	<div class="search-box <?= !empty($type) ? $type : "" ?>">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'favorite') ? 'active' : '' ?>">
                <a href="#favorite" aria-controls="favorite" role="tab" data-toggle="tab" class="favorite-class"><strong><?= \Yii::t('app', 'Favorite') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'promo') ? 'active' : '' ?>">
                <a href="#special" aria-controls="special" role="tab" data-toggle="tab" class="special-class"><strong><?= \Yii::t('app', 'Promo') ?></strong></a>
            </li>
            <li role="presentation" class="<?= $keywordType == \Yii::t('app', 'online-order') ? 'active' : '' ?>">
                <a href="#order" aria-controls="order" role="tab" data-toggle="tab" class="order-class"><strong><?= \Yii::t('app', 'Online Order') ?></strong></a>
            </li>
        </ul>
		<div class="tab-content">
    		<div role="tabpanel" class="tab-pane <?= $keywordType == \Yii::t('app', 'favorite') ? 'in active' : '' ?>" id="favorite">

            	<?= Html::beginForm(
            	    [
                	    'page/result-list',
            	        'searchType' => \Yii::t('app', 'favorite'),
            	        'city' => 'bandung',
                        'cty' => $keyword['city'],
                        'pct' => !empty($keyword['product']['id']) ? $keyword['product']['id'] : '',
                        'ctg' => !empty($keyword['category']) ? $keyword['category'] : '',
                        'pmn' => !empty($keyword['price']['min']) ? $keyword['price']['min'] : '',
                        'pmx' => !empty($keyword['price']['max']) ? $keyword['price']['max'] : '',
                        'cmp' => !empty($keyword['map']['coordinate']) ? $keyword['map']['coordinate'] : '',
                        'rmp' => !empty($keyword['map']['radius']) ? $keyword['map']['radius'] : ''
            	    ],
            	       'get',
            	    [
                        'class' => 'search-favorite'
                    ]
        	    ); ?>

                	<?= $widgetSearchInput; ?>

        		<?= Html::endForm(); ?>

    		</div>
    		<div role="tabpanel" class="tab-pane <?= $keywordType == \Yii::t('app', 'promo') ? 'in active' : '' ?>" id="special">

            	<?= Html::beginForm(['page/result-list', 'searchType' => \Yii::t('app', 'promo'), 'city' => 'bandung',
                    'cty' => $keyword['city'],
                    'pct' => !empty($keyword['product']['id']) ? $keyword['product']['id'] : '',
                    'ctg' => !empty($keyword['category']) ? $keyword['category'] : '',
                    'cmp' => !empty($keyword['map']['coordinate']) ? $keyword['map']['coordinate'] : '',
                    'rmp' => !empty($keyword['map']['radius']) ? $keyword['map']['radius'] : ''], 'get',
        	    [
                    'class' => 'search-special'
                ]); ?>

            		<?= $widgetSearchInput; ?>

            	<?= Html::endForm(); ?>

    		</div>
    		<div role="tabpanel" class="tab-pane <?= $keywordType == \Yii::t('app', 'online-order') ? 'in active' : '' ?>" id="order">

    			<?= Html::beginForm(['page/result-list', 'searchType' => \Yii::t('app', 'online-order'), 'city' => 'bandung',
                    'cty' => $keyword['city'],
                    'pct' => !empty($keyword['product']['id']) ? $keyword['product']['id'] : '',
                    'pmn' => !empty($keyword['price']['min']) ? $keyword['price']['min'] : '',
                    'pmx' => !empty($keyword['price']['max']) ? $keyword['price']['max'] : '',
                    'cmp' => !empty($keyword['map']['coordinate']) ? $keyword['map']['coordinate'] : '',
                    'rmp' => !empty($keyword['map']['radius']) ? $keyword['map']['radius'] : ''], 'get',
			    [
                    'class' => 'search-order'
                ]); ?>

        			<?= $widgetSearchInput; ?>

            	<?= Html::endForm(); ?>

    		</div>
    	</div>
	</div>

<?php
endif; ?>