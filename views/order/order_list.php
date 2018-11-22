<?php

use yii\helpers\Html;
use kartik\touchspin\TouchSpin;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = Yii::t('app', 'Order List'); ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container detail">
        
        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                        	<div class="box bg-white">
                        		<div class="box-title">
                            		<h4 class="font-alt text-center"><?= Yii::t('app', 'Order List') ?></h4>
                            	</div>
                    
                                <hr>
                                
                                <div class="box-content">
                                
                                    <?php
                                    $noDataClass = '';
                                    $urlCheckout = null;
                        
                                    $jumlahHarga = 0;
                                    
                                    if (!empty($modelTransactionSession) && !empty($modelTransactionSession['transactionItems'])):
                                    
                                        $noDataClass = 'hidden';
                                        $urlCheckout = ['order/checkout', 'id' => $modelTransactionSession['id']];
                                        
                                        foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem):
                                            
                                            $subtotal = $dataTransactionItem['businessProduct']['price'] * $dataTransactionItem['amount'];
                                            $jumlahHarga += $subtotal; ?>
                            				
                            				<div class="list-order-group">
                                        		<div class="list-order hidden-xs">
                                                	<div class="row">
                                                		
                                                		<div class="overlay" style="display: none;"></div>
                                    					<div class="loading-img" style="display: none;"></div>
                                    					
                                                    	<div class="col-sm-4">
                                                    		<h5 class="product-title"><?= $dataTransactionItem['businessProduct']['name'] ?></h5>
                                                		</div>
                                                		<div class="col-sm-2">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></h5>
                                                		</div>
                                                		<div class="col-lg-2 col-md-3 col-sm-3">
                                            
                                                            <?= TouchSpin::widget([
                                                                'name' => 'amount',
                                                                'value' => $dataTransactionItem['amount'],
                                                                'options' => [
                                                                    'class' => 'amount text-right input-sm',
                                                                    'data-url' => Yii::$app->urlManager->createUrl(['order-action/change-qty', 'id' => $dataTransactionItem['id']])
                                                                ],
                                                                'pluginOptions' => [
                                                                    'style' => 'width: 30%',
                                                                    'min' => 1,
                                                                    'max' => 50,
                                                                    'step' => 1,
                                                                    'buttonup_txt' => '<i class="glyphicon glyphicon-plus"></i>',
                                                                    'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                                                                    'buttondown_class' => "btn btn-default btn-xs",
                                                                    'buttonup_class' => "btn btn-default btn-xs"
                                                                ],
                                                            ]); ?>
                                                            
                                            			</div>
                                                		<div class="col-sm-2 subtotal">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($subtotal) ?></h5>
                                                		</div>
                                                		<div class="col-md-1 col-sm-1 text-right">
                                                		
                                                			<?= Html::a('<i class="fa fa-times fa-2x"></i>', ['order-action/remove-item', 'id' => $dataTransactionItem['id']], ['class' => 'remove-item']) ?>
                                                			
                                                		</div>
                                            		</div>
                                            		
                                            		<div class="row">
                                            			<div class="col-sm-7">
                                            			
                                            				<?= Html::textInput('menu_notes', null, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
                                            				
                                            			</div>
                                            		</div>
                                        		</div>
                                        		
                                        		
                                        		<div class="list-order visible-xs">
                                                	<div class="row">
                                                    	<div class="col-xs-9">
                                                    		<h5 class="product-title"><?= $dataTransactionItem['businessProduct']['name'] ?></h5>
                                                		</div>
                                                		<div class="col-xs-3 text-right">
                                                		
                                                			<?= Html::a('<i class="fa fa-times fa-2x"></i>', ['order-action/remove-item', 'id' => $dataTransactionItem['id']], ['class' => 'remove-item']) ?>
                                                			
                                                		</div>
                                                		<div class="col-xs-12">
                                                		
                                    						<?= Html::textInput('menu_notes', null, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
                                    						
                                            			</div>
                                            		</div>
                                            		
                                            		<br>
                                            		
                                            		<div class="row">
                                            			<div class="col-xs-12 text-right">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></h5>
                                                		</div>
                                            		</div>
                                            		
                                            		<div class="row">
                                            			<div class="col-xs-offset-6 col-xs-6">
                                            
                                                            <?= TouchSpin::widget([
                                                                'name' => 'amount',
                                                                'value' => $dataTransactionItem['amount'],
                                                                'options' => [
                                                                    'class' => 'amount text-right input-sm',
                                                                    'data-url' => Yii::$app->urlManager->createUrl(['order-action/change-qty', 'id' => $dataTransactionItem['id']])
                                                                ],
                                                                'pluginOptions' => [
                                                                    'style' => 'width: 30%',
                                                                    'min' => 1,
                                                                    'max' => 50,
                                                                    'step' => 1,
                                                                    'buttonup_txt' => '<i class="glyphicon glyphicon-plus"></i>',
                                                                    'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                                                                    'buttondown_class' => "btn btn-default btn-xs",
                                                                    'buttonup_class' => "btn btn-default btn-xs"
                                                                ],
                                                            ]); ?>
                                                            
                                            			</div>
                                            			
                                            			<div class="overlay" style="display: none;"></div>
                                    					<div class="loading-img" style="display: none;"></div>
                                    					
                                            		</div>
                                        		</div>
                                        		
                                        		
                                        		<div class="list-order visible-tab">
                                                	<div class="row">
                                                    	<div class="col-tab-6">
                                                    		<h5 class="product-title"><?= $dataTransactionItem['businessProduct']['name'] ?></h5>
                                                		</div>
                                                		<div class="col-tab-2">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></h5>
                                                		</div>
                                                		<div class="col-tab-3">
                                            
                                                            <?= TouchSpin::widget([
                                                                'name' => 'amount',
                                                                'value' => $dataTransactionItem['amount'],
                                                                'options' => [
                                                                    'class' => 'amount text-right input-sm',
                                                                    'data-url' => Yii::$app->urlManager->createUrl(['order-action/change-qty', 'id' => $dataTransactionItem['id']])
                                                                ],
                                                                'pluginOptions' => [
                                                                    'style' => 'width: 30%',
                                                                    'min' => 1,
                                                                    'max' => 50,
                                                                    'step' => 1,
                                                                    'buttonup_txt' => '<i class="glyphicon glyphicon-plus"></i>',
                                                                    'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                                                                    'buttondown_class' => "btn btn-default btn-xs",
                                                                    'buttonup_class' => "btn btn-default btn-xs"
                                                                ],
                                                            ]); ?>
                                                            
                                            			</div>
                                                		<div class="col-tab-1 text-right">
                                                		
                                                			<?= Html::a('<i class="fa fa-times fa-1x"></i>', ['order-action/remove-item', 'id' => $dataTransactionItem['id']], ['class' => 'remove-item']) ?>
                                                			
                                                		</div>
                                                		
                                                		<div class="overlay" style="display: none;"></div>
                                    					<div class="loading-img" style="display: none;"></div>
                                    					
                                            		</div>
                                            		
                                            		<div class="row">
                                            			<div class="col-tab-8">
                                    
                                    						<?= Html::textInput('menu_notes', null, ['class' => 'menu-notes', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
                                    						
                                            			</div>
                                            		</div>
                                        		</div>
                                    
                                                <hr>
                                            </div>
                                            
                                        <?php
                                        endforeach;
                                    endif; ?>
                                    
                                    <div class=<?= $noDataClass ?> id ="no-data">
                                            
                                    	<?= Yii::t('app', 'Your order list is empty') ?>
                                    	
                                    </div>
                                    
                                    <div class="row mt-70">
                                        <div class="col-sm-5 col-sm-offset-7">
                                            <div class="shop-cart-totalbox">
                                                <h4 class="font-alt"><?= Yii::t('app', 'Total Order') ?></h4>
                                                <table class="table table-responsive table-striped table-border checkout-table">
                                                    <tbody>
                                                        <tr>
                                                            <th>Total</th>
                                                            <td id="jumlah-harga"><?= Yii::$app->formatter->asCurrency($jumlahHarga) ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                        
                                                <?= Html::a('Lanjut ke Checkout', $urlCheckout, ['class' => 'btn btn-lg btn-block btn-round btn-d']) ?>
                        
                                            </div>
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
frontend\components\GrowlCustom::widget();

$jscript = '
    $(".amount").on("change", function() {

        var thisObj = $(this);
        var amount = parseInt(thisObj.val());

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "amount": amount
            },
            beforeSend: function(xhr) {

                thisObj.parent().parent().siblings(".overlay").show();
                thisObj.parent().parent().siblings(".loading-img").show();
            },
            success: function(response) {

                if (response.success) {

                    $("#jumlah-harga").html(response.total_price);
                    thisObj.parents(".list-order").find(".subtotal").children().html(response.subtotal);
                } else {

                    messageResponse(response.message.icon, response.message.title, response.message.text, response.message.type);
                }

                thisObj.parent().parent().siblings(".overlay").hide();
                thisObj.parent().parent().siblings(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.parent().parent().siblings(".overlay").hide();
                thisObj.parent().parent().siblings(".loading-img").hide();
            }
        });
    });

    $(".remove-item").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {

                thisObj.parents(".list-order").find(".overlay").show();
                thisObj.parents(".list-order").find(".loading-img").show();
            },
            success: function(response) {

                if (response.success) {

                    thisObj.parents(".list-order").fadeOut(100, function() {

                        var rootObj = $(this).parent().parent();

                        $(this).parent().remove();

                        if (rootObj.find(".list-order").length < 1) {

                            rootObj.children("#no-data").removeClass("hidden");
                        }
                    });

                    $("#jumlah-harga").html(response.total_price);
                } else {

                    messageResponse(response.message.icon, response.message.title, response.message.text, response.message.type);
                }

                thisObj.parents(".list-order").find(".overlay").show();
                thisObj.parents(".list-order").find(".loading-img").show();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.parents(".list-order").find(".overlay").show();
                thisObj.parents(".list-order").find(".loading-img").show();
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>