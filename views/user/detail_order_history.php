<?php

use yii\web\View;
use yii\helpers\Html;
use frontend\components\AddressType;
use frontend\components\GrowlCustom;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = Yii::t('app', 'Order Details'); ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">
        	
        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                
                	<?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Profile'), ['user/index']); ?>

                </div>
            </div>
        	
        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                        		
                    		<?php
                            $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 88, 88);
                            
                            if (!empty($modelTransactionSession['business']['businessImages'][0]['image'])) {
                                
                                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $modelTransactionSession['business']['businessImages'][0]['image'], 88, 88);
                            }
                            
                            $img = Html::img($img, ['class' => 'img-rounded']);
                            
                            $btnReorder = Html::a($modelTransactionSession['is_closed'] ? Yii::t('app', 'Reorder') : Yii::t('app', 'Confirmation'), ['user-action/reorder'], [
                                    'class' => 'btn btn-d btn-block btn-round btn-reorder',
                                    'data-id' => $modelTransactionSession['id']
                                ]); ?>
                    
                            <div class="row">
                            	<div class="col-xs-12">
                            		<div class="row mt-10 mb-10">
                                        <div class="col-sm-6 col-tab-7 col-xs-12">
                                            <div class="widget-posts-image image-order-history">
                                                <?= Html::a($img, ['page/detail', 'id' => $modelTransactionSession['business']['id']]) ?>
                                            </div>
                                        	<small>
                                        		<?= Yii::$app->formatter->asDate($modelTransactionSession['created_at'], 'long') . ', ' . Yii::$app->formatter->asTime($modelTransactionSession['created_at'], 'short') ?>
                                    		</small>
                                        	<br>
                                        	
                                            <?= Html::a($modelTransactionSession['business']['name'], ['page/detail', 'id' => $modelTransactionSession['business']['id']]) ?>
                                            
                                            <br>
                                            <small>
                                                <?= AddressType::widget([
                                                    'addressType' => $modelTransactionSession['business']['businessLocation']['address_type'],
                                                    'address' => $modelTransactionSession['business']['businessLocation']['address']
                                                ]); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <hr class="divider-w mb-10">
                                    
                                    <p><strong><?= Yii::t('app', 'Order Details') ?></strong></p>
                                    
                                    <div class="row mb-10">
                                    	<div class="col-xs-12">
                                    		
                                    		<?php
                                    		foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem): 
                                    		
                                    		    $amountPrice = $dataTransactionItem['amount'] . ' x ' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']); ?>
                                    		
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
                                    	<div class="col-sm-8 col-tab-8 col-xs-12">
                                    		Total : <?= Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) ?> | <i class="far fa-check-circle <?= $modelTransactionSession['is_closed'] ? 'text-success' : 'text-danger' ?>"></i>
                                    	</div>
                                    	<div class="col-sm-4 col-tab-4 text-right visible-lg visible-md visible-sm visible-tab">
                                    	
                                    		<?= $btnReorder; ?>
                                    	
                                    	</div>
                                    	<div class="col-xs-12 mt-10 visible-xs">
                                    	
                                    		<?= $btnReorder; ?>
                                    	
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
        