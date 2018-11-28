<?php

use yii\helpers\Html;
use kartik\touchspin\TouchSpin;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = Yii::t('app', 'Order List'); ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container detail">
        
        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
					
					<?php
					if (!empty($modelTransactionSession)) {
					    
					    echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back to Place Detail'), ['page/detail', 'id' => $modelTransactionSession['business_id']]);
					} else {
					  
					    echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Home Page'), ['page/index']);
					} ?>

                </div>
            </div>
            
        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                
                    <div class="row">
                        <div class="col-xs-12">
                        	<div class="box bg-white">
                        		<div class="box-title">
                            		<h4 class="font-alt text-center"><?= Yii::t('app', 'Order List') ?></h4>
                            	</div>
                    
                                <hr class="divider-w">
                                
                                <div class="box-content">
                                
                                    <?php
                                    $noDataClass = '';
                                    
                                    if (!empty($modelTransactionSession['transactionItems'])):
                                    
                                        $noDataClass = 'hidden';
                                        $removeButton = Html::a('<i class="fa fa-times fa-2x"></i>', ['order-action/remove-item'], ['class' => 'remove-item']);
                                        
                                        foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem):
                                        
                                            $notes = Html::textInput('menu_notes', $dataTransactionItem['note'], [
                                                'class' => 'form-control menu-notes',
                                                'placeholder' => Yii::t('app', 'Write a Note'),
                                                'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-notes', 'id' => $dataTransactionItem['id']])
                                            ]); ?>
                            				
                            				<div class="list-order-group">
                            				
                            					<?= Html::hiddenInput('item_id', $dataTransactionItem['id'], ['class' => 'item-id']) ?>
                            					
                            					<div class="list-order visible-lg visible-md visible-sm visible-tab">
                                                	<div class="row">
                                                    	<div class="col-lg-5 col-sm-4 col-tab-6">
                                                    		<h5 class="product-title"><?= $dataTransactionItem['businessProduct']['name'] ?></h5>
                                                		</div>
                                                		<div class="col-sm-2 col-tab-2 text-right">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></h5>
                                                		</div>
                                                		<div class="col-lg-2 col-sm-3 col-tab-3">
                                                		
                                                			<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                            
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
                                                		<div class="col-sm-2 visible-lg visible-md visible-sm subtotal">
                                                			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($dataTransactionItem['businessProduct']['price'] * $dataTransactionItem['amount']) ?></h5>
                                                		</div>
                                                		<div class="col-sm-1 col-tab-1 text-right">
                                                		
                                                			<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                                		
                                                			<?= $removeButton ?>
                                                			
                                                		</div>
                                            		</div>
                                            		
                                            		<div class="row">
                                            			<div class="col-sm-12 col-tab-12">
                                            			
                                            				<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                            			
                                            				<?= $notes ?>
                                            				
                                            			</div>
                                            		</div>
                                        		</div>
                                        		
                                        		<div class="list-order visible-xs">
                                        			<div class="row">
                                                    	<div class="col-xs-9">
                                                    		<h5 class="product-title"><?= $dataTransactionItem['businessProduct']['name'] ?></h5>
                                                		</div>
                                                		<div class="col-xs-3 text-right">
                                                		
                                                			<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                                		
                                                			<?= $removeButton ?>
                                                			
                                                		</div>
                                                		<div class="col-xs-12">
                                                		
                                                			<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                                		
                                    						<?= $notes ?>
                                    						
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
                                            			
                                            				<div class="overlay" style="display: none;"></div>
                            								<div class="loading-text" style="display: none;"></div>
                                            
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
                                            		</div>
                                        		</div>
                                    
                                                <hr>
                                            </div>
                                            
                                        <?php
                                        endforeach;
                                    endif; ?>
                                    
                                    <div class="<?= $noDataClass ?>" id="no-data">
                                            
                                    	<?= Yii::t('app', 'Your order list is empty') ?>
                                    	
                                    </div>
                                    
                                    <div class="row mt-70">
                                        <div class="col-sm-5 col-sm-offset-7">
                                            <table class="table table-responsive table-striped table-border checkout-table">
                                                <tbody>
                                                    <tr>
                                                        <th class="font-alt">Total</th>
                                                        <td id="total-price"><?= Yii::$app->formatter->asCurrency(!empty($modelTransactionSession['total_price']) ? $modelTransactionSession['total_price'] : 0) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                    						
                    						<?php
                    						if (!empty($modelTransactionSession)) {
                    						    
                						        echo Html::a(Yii::t('app', 'Go to Checkout'), ['order/checkout', 'id' => $modelTransactionSession['id']], ['class' => 'btn btn-lg btn-block btn-round btn-d checkout-btn']);
                    						} else {
                    						    
                    						    echo Html::a(Yii::t('app', 'Go to Checkout'), null, ['class' => 'btn btn-lg btn-block btn-round btn-d checkout-btn', 'disabled' => 'disabled']);
                    						} ?>
                                            
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

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    $("#total-price").html(response.total_price);
                    thisObj.parents(".list-order").find(".subtotal").children().html(response.subtotal);
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.parent().siblings(".overlay").hide();
                thisObj.parent().siblings(".loading-text").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.parent().siblings(".overlay").hide();
                thisObj.parent().siblings(".loading-text").hide();
            }
        });
    });

    $(".remove-item").on("click", function() {

        var thisObj = $(this);
        var itemID = thisObj.parents(".list-order-group").find(".item-id").val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "item_id": itemID,
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    var rootObj = thisObj.parents(".box-content");
    
                    thisObj.parents(".list-order-group").remove();
    
                    if (rootObj.find(".list-order-group").length < 1) {
    
                        rootObj.children("#no-data").removeClass("hidden");

                        $(".checkout-btn").attr("disabled", "disabled");

                        $(".checkout-btn").on("click", function(e) {
                
                            e.preventDefault();
                        });
                    }

                    $("#total-price").html(response.total_price);
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-text").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-text").hide();
            }
        });

        return false;
    });

    $(".menu-notes").on("change", function() {
        
        var thisObj = $(this);
        var note = thisObj.val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "note": note
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-text").show();
            },
            success: function(response) {

                if (!response.success) {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-text").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-text").hide();
            }
        });
    });
';

$this->registerJs($jscript); ?>