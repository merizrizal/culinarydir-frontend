<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use frontend\components\AddressType;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-order-history a',
    'options' => ['id' => 'pjax-order-history-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-order-history', 'class' => 'pagination'],
]); ?>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>


<div class="row" style="position: relative;">
    <div class="order-history-container">
    
    	<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>
		
        <?php
        if (!empty($modelTransactionSession)):
            
            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
            foreach ($modelTransactionSession as $dataTransactionSession):
            
                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 88, 88);
                
                if (!empty($dataTransactionSession['business']['businessImages'][0]['image'])) {
                    
                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataTransactionSession['business']['businessImages'][0]['image'], 88, 88);
                }
                
                $img = Html::img($img, ['class' => 'img-rounded']); ?>
        
                <div class="row">
                	<div class="col-xs-12">
                		<div class="row mt-10 mb-10">
                            <div class="col-md-6 col-sm-6 col-tab-7 col-xs-12">
                                <div class="widget-posts-image image-order-history">
                                    <?= Html::a($img, ['page/detail', 'id' => $dataTransactionSession['business']['id']]) ?>
                                </div>
                                
                            	<small><?= Yii::$app->formatter->asDate($dataTransactionSession['updated_at'], 'long') . ', ' . Yii::$app->formatter->asTime($dataTransactionSession['updated_at'], 'short') ?></small>
                            	<br>
                                <?= Html::a($dataTransactionSession['business']['name'], ['page/detail', 'id' => $dataTransactionSession['business']['id']]) ?>
                                <br>
                                <small>
                                    <?= AddressType::widget([
                                        'addressType' => $dataTransactionSession['business']['businessLocation']['address_type'],
                                        'address' => $dataTransactionSession['business']['businessLocation']['address']
                                    ]); ?>
                                </small>
                            </div>
                        </div>
                        <div class="row mb-10">
                        	<div class="col-md-6 col-sm-6 col-tab-7 col-xs-12">
                        		Total : <?= Yii::$app->formatter->asCurrency($dataTransactionSession['total_price']) ?> | <strong><?= $dataTransactionSession['is_closed'] ? Yii::t('app', 'Done') : Yii::t('app', 'Not Done') ?></strong>
                        	</div>
                        </div>
                	</div>
                </div>
                
                <hr class="divider-w">
        
        	<?php
            endforeach;
            
            Yii::$app->formatter->timeZone = 'UTC';
        
        else: ?>
            
            <div class="row">
            	<div class="col-xs-12">
            		<p><?= Yii::t('app', 'You don\'t Have Any Order Histories Right Now') ?></p>
            	</div>
            </div>
            
        <?php
        endif; ?>
        
    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
$jscript = '
    $(".total-order-history").html(' . $totalCount . ');

    $("#pjax-order-history-container").off("pjax:send");
    $("#pjax-order-history-container").on("pjax:send", function() {

        $(".order-history-container").children(".overlay").show();
        $(".order-history-container").children(".loading-img").show();
    });

    $("#pjax-order-history-container").off("pjax:complete");
    $("#pjax-order-history-container").on("pjax:complete", function() {

        $(".order-history-container").children(".overlay").hide();
        $(".order-history-container").children(".loading-img").hide();
    });

    $("#pjax-order-history-container").off("pjax:error");
    $("#pjax-order-history-container").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>