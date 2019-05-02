<?php

use frontend\components\AddressType;
use frontend\components\GrowlCustom;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = \Yii::t('app', 'Order Details');

$urlBusinessDetail = [
    'page/detail',
    'city' => Inflector::slug($modelTransactionSession['business']['businessLocation']['city']['name']),
    'uniqueName' => $modelTransactionSession['business']['unique_name']
];

$img = (!empty($modelTransactionSession['business']['businessImages']) ? $modelTransactionSession['business']['businessImages'][0]['image'] : '') . '&w=88&h=88'; ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">

        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                	<?= Html::a('<i class="fa fa-angle-double-left"></i> ' . \Yii::t('app', 'Back To Profile'), ['user/index']); ?>
                </div>
            </div>

        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">

                            <div class="row">
                            	<div class="col-xs-12">
                            		<div class="row mt-10 mb-10">
                                        <div class="col-sm-6 col-tab-7 col-xs-12">
                                            <div class="widget-posts-image image-order-history">
                                                <?= Html::a(Html::img(\Yii::$app->params['endPointLoadImage'] . 'registry-business?image=' . $img, ['class' => 'img-rounded']), $urlBusinessDetail) ?>
                                            </div>
                                        	<small>
                                        		<?= \Yii::$app->formatter->asDate($modelTransactionSession['created_at'], 'long') . ', ' . \Yii::$app->formatter->asTime($modelTransactionSession['created_at'], 'short') ?>
                                    		</small>
                                        	<br>
                                        		<?= Html::a($modelTransactionSession['business']['name'], $urlBusinessDetail) ?>
                                            <br>
                                            <small>
                                                <?= AddressType::widget(['businessLocation' => $modelTransactionSession['business']['businessLocation']]); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <hr class="divider-w mb-10">

                                    <p><strong><?= \Yii::t('app', 'Order Details') ?></strong></p>

                                    <div class="row mb-10">
                                    	<div class="col-xs-12">

                                    		<?php
                                    		foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem):

                                    		    $amountPrice = $dataTransactionItem['amount'] . ' x ' . \Yii::$app->formatter->asCurrency($dataTransactionItem['price']); ?>

                                        		<div class="row mb-10">
                                        			<div class="col-xs-12">
                                            			<div class="row">
                                                            <div class="col-sm-9 col-tab-8 col-xs-12">
                                                                <strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
                                                            </div>
                                                            <div class="col-sm-3 col-tab-4 text-right visible-lg visible-md visible-sm visible-tab">
                                                                <strong><?= $amountPrice ?></strong>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                        	<div class="col-xs-12">
                                                        		<small><?= $dataTransactionItem['note'] ?></small>
                                                    		</div>
                                                    		<div class="col-xs-12 visible-xs">
                                                            	<strong><?= $amountPrice ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

        	   	                            <?php
                                    		endforeach; ?>

                                    	</div>
                                    </div>

                                    <hr class="divider-w mb-10">

                                    <div class="row">
                                    	<div class="col-sm-8 col-tab-8 col-xs-12 mb-10">

                                    		<?php
                                    		$grandTotal = $modelTransactionSession['total_price'] - $modelTransactionSession['discount_value'];
                                    		$checkSymbol = ' | <i class="far fa-check-circle ' . ($modelTransactionSession['is_closed'] ? "text-success" : "text-danger") . '"></i>';

                                    		if (!empty($modelTransactionSession['discount_value'])) {

                                    		    echo 'Subtotal : ' . \Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) . '
                                                    <br>Promo : ' . \Yii::$app->formatter->asCurrency($modelTransactionSession['discount_value']) . '
                                                    <br>Grand Total : ' . \Yii::$app->formatter->asCurrency($grandTotal < 0 ? 0 : $grandTotal) . $checkSymbol;
                                    		} else {

                                    		    echo 'Grand Total : ' . \Yii::$app->formatter->asCurrency($grandTotal) . $checkSymbol;
                                    		} ?>

                                    	</div>
                                    	<div class="col-sm-4 col-tab-4 col-xs-12">

                                    		<?= Html::a($modelTransactionSession['is_closed'] ? \Yii::t('app', 'Reorder') : \Yii::t('app', 'Confirmation'), ['user-action/reorder'], [
                                                'class' => 'btn btn-d btn-block btn-round btn-reorder',
                                                'data-id' => $modelTransactionSession['id']
                                            ]); ?>

                                    	</div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
';

$this->registerJs($jscript); ?>
