<?php

use frontend\components\AddressType;
use frontend\components\GrowlCustom;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

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

        <?= \Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

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

            foreach ($modelTransactionSession as $dataTransactionSession):

                $img = (!empty($dataTransactionSession['business']['businessImages']) ? $dataTransactionSession['business']['businessImages'][0]['image'] : '') . '&w=88&h=88';

                $btnDetail = Html::a('<i class="fas fa-search"></i> Detail', ['user/detail-order-history', 'id' => $dataTransactionSession['id']], [
                    'class' => 'btn btn-default btn-small btn-round-4'
                ]);

                $btnReorder = Html::a($dataTransactionSession['is_closed'] ? '<i class="aicon aicon-icon-online-ordering"></i> ' . \Yii::t('app', 'Reorder') : '<i class="aicon aicon-inspection_checklist"></i> ' . \Yii::t('app', 'Confirmation'), ['user-action/reorder'], [
                    'class' => 'btn btn-default btn-small btn-round-4 btn-reorder',
                    'data-id' => $dataTransactionSession['id']
                ]);

                $urlBusinessDetail = [
                    'page/detail',
                    'city' => Inflector::slug($dataTransactionSession['business']['businessLocation']['city']['name']),
                    'uniqueName' => $dataTransactionSession['business']['unique_name']
                ];

                $grandTotal = $dataTransactionSession['total_price'] - $dataTransactionSession['discount_value']; ?>

            	<div class="col-xs-12">
            		<div class="row mb-10">
                        <div class="col-sm-6 col-tab-7 col-xs-12">
                            <div class="widget-posts-image image-order-history">
                                <?= Html::a(Html::img(\Yii::$app->params['endPointLoadImage'] . 'registry-business?image=' . $img, ['class' => 'img-rounded']), $urlBusinessDetail) ?>
                            </div>
                        	<small>
                        		<?= \Yii::$app->formatter->asDate($dataTransactionSession['created_at'], 'long') . ', ' . \Yii::$app->formatter->asTime($dataTransactionSession['created_at'], 'short') ?>
                    		</small>
                        	<br>
                            	<?= Html::a($dataTransactionSession['business']['name'], $urlBusinessDetail) ?>
                            <br>
                            <small>
                                <?= AddressType::widget(['businessLocation' => $dataTransactionSession['business']['businessLocation']]); ?>
                            </small>
                        </div>
                    </div>
                    <div class="row mb-10">
                    	<div class="col-sm-7 col-tab-6 col-xs-12">
                    		Grand Total : <?= \Yii::$app->formatter->asCurrency($grandTotal < 0 ? 0 : $grandTotal) ?> | <i class="far fa-check-circle <?= $dataTransactionSession['is_closed'] ? 'text-success' : 'text-danger' ?>"></i>
                    	</div>
                    	<div class="col-sm-5 col-tab-6 text-right visible-lg visible-md visible-sm visible-tab">
                    		<ul class="list-inline list-review mt-0 mb-0">
                                <li><?= $btnDetail ?></li>
                                <li><?= $btnReorder ?></li>
                            </ul>
                    	</div>
                    	<div class="col-xs-12 visible-xs">
                    		<ul class="list-inline list-review mt-10 mb-0">
                                <li><?= $btnDetail ?></li>
                                <li><?= $btnReorder ?></li>
                            </ul>
                    	</div>
                    </div>

                    <hr class="divider-w mt-10 mb-10">
            	</div>

        	<?php
            endforeach;
        endif; ?>

    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= \Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 col-tab-6 visible-lg visible-md visible-sm visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
GrowlCustom::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $(".btn-reorder").on("click", function() {

        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("href"),
            data: {
                "id": $(this).data("id"),
            },
            success: function(response) {

                if (!response.success) {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });

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
    $("#pjax-order-history-container").on("pjax:error", function(event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>