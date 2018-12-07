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
											    
											    echo Html::hiddenInput('session_id', $modelTransactionSession['id'], ['class' => 'session-id']);
											}
											
                                            if (!empty($modelBusiness['businessProducts'])):
                                        
                                                foreach ($modelBusiness['businessProducts'] as $dataBusinessProduct):
                                                
                                                    $existOrderClass = 'hidden';
                                                    $addOrderClass = '';
                                                    $itemId = null;
                                                    $itemNotes = null;
                                                    $amountItem = 1;
                                            
                                                    if (!empty($modelTransactionSession['transactionItems'])) {
                                                        
                                                        foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem) {
                                                            
                                                            if ($dataBusinessProduct['id'] === $dataTransactionItem['business_product_id']) {
                                                                
                                                                $existOrderClass = '';
                                                                $addOrderClass = 'hidden';
                                                                $itemId = $dataTransactionItem['id'];
                                                                $itemNotes = $dataTransactionItem['note'];
                                                                $amountItem = $dataTransactionItem['amount'];
                                                                
                                                                break;
                                                            }
                                                        }
                                                    } ?>
                    								
                    								<div class="business-menu mb-20">
                                                        
                                                        <?= Html::hiddenInput('product_id', $itemId, ['class' => 'item-id']); ?>
                                                        
                                                        <div class="row">
                                                            <div class="col-sm-8 col-xs-7">
                                                                <strong class="menu-name"><?= $dataBusinessProduct['name'] ?></strong>
                                                            </div>
                                                            <div class="col-sm-2 col-xs-3">
                                                                <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                                            </div>
                                                            <div class="col-xs-2 text-right <?= $existOrderClass ?>">
                                                		
                                                    			<div class="overlay" style="display: none;"></div>
                                                    		
                                                    			<?= Html::a('<i class="fa fa-times"></i>', ['order-action/remove-item'], ['class' => 'remove-item']); ?>
                                                    			
                                                    		</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                <p class="mb-0"><?= $dataBusinessProduct['description'] ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="row <?= $addOrderClass ?>">
                                                        	<div class="col-sm-offset-8 col-sm-4 col-xs-offset-7 col-xs-5">
                                                        	
                                                        		<?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Order This'), [
                                                        		    'class' => 'btn btn-d btn-round btn-xs add-item',
                                                        		    'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-order']),
                                                        		    'data-menuid' => $dataBusinessProduct['id'],
                                                        		    'data-menuprice' => $dataBusinessProduct['price']
                                                        		]) ?>
                                                            	
                                                        	</div>
                                                        </div>
                                                        <div class="row input-order <?= $existOrderClass ?>">
                                                        	<div class="col-sm-8 col-xs-7">
                                                    		
                                                    			<div class="overlay" style="display: none;"></div>
                                                    			<div class="loading-text" style="display: none;"></div>
                                                    			
                                                    			<?= Html::textInput('item_notes', $itemNotes, [
                                                                    'class' => 'form-control item-notes',
                                                                    'placeholder' => Yii::t('app', 'Note'),
                                                                    'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-notes'])
                                                                ]); ?>
                                                    			
                                                    		</div>
                                                        	<div class="col-sm-2 col-xs-3">
                                                    	
                                                    			<div class="overlay" style="display: none;"></div>
                                                    			<div class="loading-text" style="display: none;"></div>
                                                    
                                                                <?= TouchSpin::widget([
                                                                    'name' => 'amount_item',
                                                                    'value' => $amountItem,
                                                                    'options' => [
                                                                        'class' => 'amount-item text-right input-sm',
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

    if ($(".session-id").length) {

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
                "business_name": $(".business-name").val(),
                "menu_id": thisObj.data("menuid"),
                "menu_price": thisObj.data("menuprice")
            },
            success: function(response) {
                
                if (response.success) {
                
                    if (cart != null) {
    
                        cart.update("title", "<b>" + response.total_amount + " menu" + " | total : " + response.total_price + "</b>");
                    } else {
    
                        cart = stickyGrowl(
                            "aicon aicon-icon-online-ordering aicon-1x", 
                            "<b>" + response.total_amount + " menu | total : " + response.total_price + "</b>", 
                            response.business_name, 
                            "info"
                        );
                    }

                    thisObj.parent().parent().addClass("hidden");
                    thisObj.parents(".business-menu").find(".input-order").removeClass("hidden");
                    thisObj.parents(".business-menu").find(".remove-item").parent().removeClass("hidden");
                    thisObj.parents(".business-menu").find(".item-id").val(response.item_id);
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $(".amount-item").on("change", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": $(this).parents(".business-menu").find(".item-id").val(),
                "amount": parseInt(thisObj.val())
            },
            beforeSend: function(xhr) {

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (!response.success) {

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

    $(".item-notes").on("change", function() {
        
        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": $(this).parents(".business-menu").find(".item-id").val(),
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

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

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
                "id": $(this).parents(".business-menu").find(".item-id").val()
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
    
                    thisObj.parent().addClass("hidden");
                    thisObj.parents(".business-menu").find(".input-order").addClass("hidden");
                    thisObj.parents(".business-menu").find(".add-item").parent().parent().removeClass("hidden");
                    thisObj.parents(".business-menu").find(".item-id").val("");
                } else {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.children().removeClass("fa-spinner fa-spin").addClass("fa-times");
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.children().removeClass("fa-spinner fa-spin").addClass("fa-times");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>