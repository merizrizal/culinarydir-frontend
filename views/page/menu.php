<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\touchspin\TouchSpin;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $modelBusiness core\models\Business */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = Yii::t('app', 'Product'); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail">
        
        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back to Place Detail'), ['page/detail', 'id' => $modelBusiness['id']]); ?>

                </div>
            </div>
        
        	<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            
            		<div class="row">
            			<div class="col-sm-12 col-xs-12">
            				<div class="box bg-white">
            					<div class="box-title">
            						<h4 class="font-alt text-center"><?= Yii::t('app', 'Product') ?></h4>
            					</div>
            					
            					<hr class="divider-w">
            					
            					<div class="box-content">
            						<div class="row">
										<div class="col-xs-12">
										
											<?php
											echo Html::hiddenInput('business_id', $modelBusiness['id'], ['class' => 'business-id']);
											echo Html::hiddenInput('business_name', $modelBusiness['name'], ['class' => 'business-name']);
											
											if (!empty($modelTransactionSession)) {
											    
											    echo Html::hiddenInput('transaction_session_id', $modelTransactionSession['id'], ['class' => 'transaction-session-id']);
											}
											
                                            if (!empty($modelBusiness['businessProducts'])):
                                        
                                                foreach ($modelBusiness['businessProducts'] as $dataBusinessProduct):
                                                
                                                    $existOrderClass = 'hidden';
                                                    $addOrderClass = '';
                                                    $transactionItemId = null;
                                                    $transactionItemNotes = null;
                                                    $transactionItemAmount = 1;
                                            
                                                    if (!empty($modelTransactionSession['transactionItems'])) {
                                                        
                                                        foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem) {
                                                            
                                                            if ($dataBusinessProduct['id'] === $dataTransactionItem['business_product_id']) {
                                                                
                                                                $existOrderClass = '';
                                                                $addOrderClass = 'hidden';
                                                                $transactionItemId = $dataTransactionItem['id'];
                                                                $transactionItemNotes = $dataTransactionItem['note'];
                                                                $transactionItemAmount = $dataTransactionItem['amount'];
                                                                
                                                                break;
                                                            }
                                                        }
                                                    } ?>
                                                    
                                                    <div class="business-menu-group">
                            							<div class="business-menu mb-20 visible-lg visible-md visible-sm visible-tab">
                            								
                            								<?= Html::hiddenInput('transaction_item_id', $transactionItemId, ['class' => 'transaction-item-id']); ?>
                                                        
                                                            <div class="row">
                                                                <div class="col-sm-8 col-tab-8">
                                                                    <strong><?= $dataBusinessProduct['name'] ?></strong>
                                                                </div>
                                                                <div class="col-sm-3 col-tab-3">
                                                                    <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                                                </div>
                                                                <div class="col-sm-1 col-tab-1 text-right <?= $existOrderClass ?>">
                                                    		
                                                        			<div class="overlay" style="display: none;"></div>
                                                        		
                                                        			<?= Html::a('<i class="fa fa-times"></i>', ['order-action/remove-item'], ['class' => 'remove-item']); ?>
                                                        			
                                                        		</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-12 col-tab-12">
                                                                    <p class="mb-0"><?= $dataBusinessProduct['description'] ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="row <?= $addOrderClass ?>">
                                                            	<div class="col-xs-offset-8 col-xs-4">
                                                            	
                                                            		<?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Order This'), [
                                                            		    'class' => 'btn btn-d btn-round btn-xs add-item',
                                                            		    'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-order']),
                                                            		    'data-product-id' => $dataBusinessProduct['id'],
                                                            		    'data-product-price' => $dataBusinessProduct['price']
                                                            		]) ?>
                                                                	
                                                            	</div>
                                                            </div>
                                                            <div class="row input-order <?= $existOrderClass ?>">
                                                            	<div class="col-sm-8 col-tab-8">
                                                        		
                                                        			<div class="overlay" style="display: none;"></div>
                                                        			<div class="loading-text" style="display: none;"></div>
                                                        			
                                                        			<?= Html::textInput('transaction_item_notes', $transactionItemNotes, [
                                                                        'class' => 'form-control transaction-item-notes',
                                                                        'placeholder' => Yii::t('app', 'Note'),
                                                                        'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-notes'])
                                                                    ]); ?>
                                                        			
                                                        		</div>
                                                            	<div class="col-lg-2 col-sm-3 col-tab-3">
                                                        	
                                                        			<div class="overlay" style="display: none;"></div>
                                                        			<div class="loading-text" style="display: none;"></div>
                                                        
                                                                    <?= TouchSpin::widget([
                                                                        'name' => 'transaction_item_amount',
                                                                        'value' => $transactionItemAmount,
                                                                        'options' => [
                                                                            'class' => 'transaction-item-amount text-right input-sm',
                                                                            'data-url' => Yii::$app->urlManager->createUrl(['order-action/change-qty'])
                                                                        ],
                                                                        'pluginOptions' => [
                                                                            'style' => 'width: 30%',
                                                                            'min' => 1,
                                                                            'max' => 50,
                                                                            'step' => 1,
                                                                            'buttonup_txt' => '<i class="glyphicon glyphicon-plus"></i>',
                                                                            'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                                                                            'buttondown_class' => "btn btn-default text-center",
                                                                            'buttonup_class' => "btn btn-default text-center"
                                                                        ],
                                                                    ]); ?>
                                                                    
                                                        		</div>
                                                            </div>
                            							</div>
                            							<div class="business-menu mb-20 visible-xs">
                            							
                            								<?= Html::hiddenInput('transaction_item_id', $transactionItemId, ['class' => 'transaction-item-id']); ?>
                                                        
                                                            <div class="row mb-10">
                                                                <div class="col-xs-7">
                                                                    <strong><?= $dataBusinessProduct['name'] ?></strong>
                                                                </div>
                                                                <div class="col-xs-5 product-price <?= $addOrderClass ?>">
                                                                    <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                                                </div>
                                                                <div class="col-xs-5 text-right <?= $existOrderClass ?>">
                                                    		
                                                        			<div class="overlay" style="display: none;"></div>
                                                        		
                                                        			<?= Html::a('<i class="fa fa-times"></i>', ['order-action/remove-item'], ['class' => 'remove-item']); ?>
                                                        			
                                                        		</div>
                                                            </div>
                                                            <div class="row <?= $addOrderClass ?>">
                                                            	<div class="col-xs-offset-7 col-xs-5">
                                                            	
                                                            		<?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Order This'), [
                                                            		    'class' => 'btn btn-d btn-round btn-xs add-item',
                                                            		    'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-order']),
                                                            		    'data-product-id' => $dataBusinessProduct['id'],
                                                            		    'data-product-price' => $dataBusinessProduct['price']
                                                            		]) ?>
                                                                	
                                                            	</div>
                                                            </div>
                                                            <div class="row input-order <?= $existOrderClass ?>">
                                                            	<div class="col-xs-12 mb-10">
                                                        		
                                                        			<div class="overlay" style="display: none;"></div>
                                                        			<div class="loading-text" style="display: none;"></div>
                                                        			
                                                        			<?= Html::textInput('transaction_item_notes', $transactionItemNotes, [
                                                                        'class' => 'form-control transaction-item-notes',
                                                                        'placeholder' => Yii::t('app', 'Note'),
                                                                        'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-notes'])
                                                                    ]); ?>
                                                        			
                                                        		</div>
                                                                <div class="col-xs-7">
                                                                    <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                                                </div>
                                                            	<div class="col-xs-5">
                                                        	
                                                        			<div class="overlay" style="display: none;"></div>
                                                        			<div class="loading-text" style="display: none;"></div>
                                                        
                                                                    <?= TouchSpin::widget([
                                                                        'name' => 'transaction_item_amount',
                                                                        'value' => $transactionItemAmount,
                                                                        'options' => [
                                                                            'class' => 'transaction-item-amount text-right input-sm',
                                                                            'data-url' => Yii::$app->urlManager->createUrl(['order-action/change-qty'])
                                                                        ],
                                                                        'pluginOptions' => [
                                                                            'style' => 'width: 30%',
                                                                            'min' => 1,
                                                                            'max' => 50,
                                                                            'step' => 1,
                                                                            'buttonup_txt' => '<i class="glyphicon glyphicon-plus"></i>',
                                                                            'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                                                                            'buttondown_class' => "btn btn-default text-center",
                                                                            'buttonup_class' => "btn btn-default text-center"
                                                                        ],
                                                                    ]); ?>
                                                                    
                                                        		</div>
                                                            </div>
                            							</div>
                        							</div>
                    
                                                <?php
                                                endforeach;
                                            else: ?>
                            
                                            	<p><?= Yii::t('app', 'Currently there is no menu available') . '.' ?> </p>
                            
                                            <?php
                                            endif; ?>
											
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

$this->registerJs(GrowlCustom::messageResponse() . GrowlCustom::stickyResponse(), View::POS_HEAD);

$totalPrice = !empty($modelTransactionSession['total_price']) ? Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) : '';

$jscript = '
    var cart = null;

    if ($(".transaction-session-id").length) {

        cart = stickyGrowl(
            "aicon aicon-icon-online-ordering aicon-1x",
            "' . $modelTransactionSession['total_amount'] . '" + " menu | total : " + "' . $totalPrice . '",
            "' . $modelTransactionSession['business']['name'] . '",
            "info"
        );
    }

    $(".add-item").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "business_id": $(".business-id").val(),
                "product_id": thisObj.data("product-id"),
                "product_price": thisObj.data("product-price")
            },
            success: function(response) {
                
                if (response.success) {
                
                    if (cart != null) {
    
                        cart.update("title", "<b>" + response.total_amount + " menu" + " | total : " + response.total_price + "</b>");
                    } else {
    
                        cart = stickyGrowl(
                            "aicon aicon-icon-online-ordering aicon-1x", 
                            "<b>" + response.total_amount + " menu | total : " + response.total_price + "</b>", 
                            $(".business-name").val(), 
                            "info"
                        );
                    }

                    var parentClass = thisObj.parents(".business-menu");

                    thisObj.parent().parent().addClass("hidden");
                    parentClass.find(".input-order").removeClass("hidden");
                    parentClass.find(".remove-item").parent().removeClass("hidden");
                    parentClass.find(".transaction-item-id").val(response.item_id);
                    parentClass.find(".product-price").addClass("hidden");
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $(".transaction-item-amount").on("change", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": $(this).parents(".business-menu").find(".transaction-item-id").val(),
                "amount": parseInt(thisObj.val())
            },
            beforeSend: function(xhr) {

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    cart.update("title", "<b>" + response.total_amount + " menu" + " | total : " + response.total_price + "</b>");
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.parent().siblings(".overlay").hide();
                thisObj.parent().siblings(".loading-text").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.parent().siblings(".overlay").hide();
                thisObj.parent().siblings(".loading-text").hide();
            }
        });
    });

    $(".transaction-item-notes").on("change", function() {
        
        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": $(this).parents(".business-menu").find(".transaction-item-id").val(),
                "note": thisObj.val()
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

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-text").hide();
            }
        });
    });

    $(".remove-item").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: { 
                "id": $(this).parents(".business-menu").find(".transaction-item-id").val()
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.children().removeClass("fa-times").addClass("fa-spinner fa-spin");
            },
            success: function(response) {

                if (response.success) {

                    if (!response.total_amount) {
                        cart.close();
                        cart = null;
                    } else {
                        cart.update("title", "<b>" + response.total_amount + " menu" + " | total : " + response.total_price + "</b>");
                    }

                    var parentClass = thisObj.parents(".business-menu");
    
                    thisObj.parent().addClass("hidden");
                    parentClass.find(".input-order").addClass("hidden");
                    parentClass.find(".add-item").parent().parent().removeClass("hidden");
                    parentClass.find(".product-price").removeClass("hidden");
                    parentClass.find(".transaction-item-id").val("");
                    parentClass.find(".transaction-item-notes").val("");
                    parentClass.find(".transaction-item-amount").val(1);
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.children().removeClass("fa-spinner fa-spin").addClass("fa-times");
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.children().removeClass("fa-spinner fa-spin").addClass("fa-times");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>