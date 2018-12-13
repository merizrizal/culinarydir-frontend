<?php

use frontend\components\AddressType;
use sycomponent\Tools;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = Yii::t('app', 'Order Details'); ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">
        	
        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                
                	<?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Profile'), ['user/index']); ?>

                </div>
            </div>
        	
        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                        	<div class="detail-order-history">
                        		
                        		<?php
                                Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                                
                                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 88, 88);
                                
                                if (!empty($modelTransactionSession['business']['businessImages'][0]['image'])) {
                                    
                                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $modelTransactionSession['business']['businessImages'][0]['image'], 88, 88);
                                }
                                
                                $img = Html::img($img, ['class' => 'img-rounded']); ?>
                        
                                <div class="row">
                                	<div class="col-xs-12">
                                		<div class="row mt-10 mb-10">
                                            <div class="col-md-6 col-sm-6 col-tab-7 col-xs-12">
                                                <div class="widget-posts-image image-order-history">
                                                    <?= Html::a($img, ['page/detail', 'id' => $modelTransactionSession['business']['id']]) ?>
                                                </div>
                                                
                                            	<small><?= Yii::$app->formatter->asDate($modelTransactionSession['updated_at'], 'long') . ', ' . Yii::$app->formatter->asTime($modelTransactionSession['updated_at'], 'short') ?></small>
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
                                                                <div class="col-md-9 col-sm-9 col-tab-8 col-xs-12">
                                                                    <?= $dataTransactionItem['businessProduct']['name'] ?>
                                                                </div>
                                                                <div class="col-md-3 col-sm-3 col-tab-4 text-right visible-lg visible-md visible-sm visible-tab">
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
                                        
                                        <div class="row mb-10">
                                        	<div class="col-md-10 col-sm-10 col-tab-10 col-xs-8">
                                        		Total : <?= Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) ?> | <i class="far fa-check-circle" style="color: <?= $modelTransactionSession['is_closed'] ? 'green' : 'red' ?>"></i>
                                        	</div>
                                        </div>
                                	</div>
                                </div>
                            
                            	<?php
                                Yii::$app->formatter->timeZone = 'UTC'; ?>
                        		
                        	</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
        