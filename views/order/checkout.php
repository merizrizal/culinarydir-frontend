<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\touchspin\TouchSpin;
use frontend\components\GrowlCustom;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */
/* @var $modelTransactionSessionOrder core\models\TransactionSessionOrder */

$this->title = 'Checkout'; ?>

<div class="main">

	<section class="module-extra-small bg-main">
		<div class="container detail checkout-order">
		
			<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                
                	<?php
					if (!empty($modelTransactionSession)) {
					    
					    echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Continue Ordering'), ['page/menu', 'id' => $modelTransactionSession['business']['id']]);
					} else {
					  
					    echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Home Page'), ['page/index']);
					} ?>

                </div>
            </div>
		
			<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            
            		<div class="row">
            			<div class="col-sm-12 col-xs-12">        					
            				<div class="box bg-white">
            					<div class="box-title">
            						<h4 class="font-alt text-center"><?= Yii::t('app', 'Order Confirmation') ?></h4>
            					</div>
            					
            					<hr class="divider-w">
            					
            					<div class="box-content">
            					
                					<?php
                					$form = ActiveForm::begin([
                					    'id' => 'checkout-form',
                					    'action' => ['order/checkout'],
                					    'fieldConfig' => [
                					        'template' => '{input}{error}',
                					    ]
                					]);
                					
                						echo Html::hiddenInput('transaction_id', $modelTransactionSession['id']); ?>
                					
                						<div class="row">
                							<div class="col-xs-12">
                								
                								<?php
                								if (($hiddenClass = !empty($modelTransactionSession))):
                								
                    								foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem): ?>
                                                        
                                                        <div class="business-menu-group">
                                                        
                                                        	<?= Html::hiddenInput('transaction_item_id', $dataTransactionItem['id'], ['class' => 'transaction-item-id']); ?>
                                                        
                                							<div class="business-menu mb-20 visible-lg visible-md visible-sm visible-tab">
                                								<div class="row">
                                                                    <div class="col-sm-8 col-tab-8">
                                                                        <strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
                                                                    </div>
                                                                    <div class="col-sm-3 col-tab-3">
                                                                        <strong><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></strong>
                                                                    </div>
                                                                    <div class="col-sm-1 col-tab-1 text-right">
                                                            			<div class="overlay" style="display: none;"></div>
                                                            			<?= Html::a('<i class="fa fa-times"></i>', ['order-action/remove-item'], ['class' => 'remove-item']); ?>
                                                            		</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12 col-tab-12">
                                                                        <p class="mb-0"><?= $dataTransactionItem['businessProduct']['description'] ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                	<div class="col-sm-8 col-tab-8">
                                                            		
                                                            			<div class="overlay" style="display: none;"></div>
                                                            			<div class="loading-text" style="display: none;"></div>
                                                            			
                                                            			<?= Html::textInput('transaction_item_notes', $dataTransactionItem['note'], [
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
                                                                            'value' => $dataTransactionItem['amount'],
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
                                								<div class="row mb-10">
                                                                    <div class="col-xs-10">
                                                                        <strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
                                                                    </div>
                                                                    <div class="col-xs-2 text-right">
                                                            			<div class="overlay" style="display: none;"></div>
                                                            			<?= Html::a('<i class="fa fa-times"></i>', ['order-action/remove-item'], ['class' => 'remove-item']); ?>
                                                            		</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-xs-12">
                                                                        <p class="mb-0"><?= $dataTransactionItem['businessProduct']['description'] ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                	<div class="col-xs-12 mb-10">
                                                            		
                                                            			<div class="overlay" style="display: none;"></div>
                                                            			<div class="loading-text" style="display: none;"></div>
                                                            			
                                                            			<?= Html::textInput('transaction_item_notes', $dataTransactionItem['note'], [
                                                                            'class' => 'form-control transaction-item-notes',
                                                                            'placeholder' => Yii::t('app', 'Note'),
                                                                            'data-url' => Yii::$app->urlManager->createUrl(['order-action/save-notes'])
                                                                        ]); ?>
                                                            			
                                                            		</div>
                                                                    <div class="col-xs-7">
                                                                        <strong><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></strong>
                                                                    </div>
                                                                	<div class="col-xs-5">
                                                            	
                                                            			<div class="overlay" style="display: none;"></div>
                                                            			<div class="loading-text" style="display: none;"></div>
                                                            
                                                                        <?= TouchSpin::widget([
                                                                            'name' => 'transaction_item_amount',
                                                                            'value' => $dataTransactionItem['amount'],
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
                                                        
                                                        <br>
                                                        
                                                    <?php
                                                    endforeach;  ?>
                                                        
                                                    <div class="row mb-30">
                                                    	<h5 class="font-alt text-center"><?= Yii::t('app', 'Delivery Methods') ?></h5><hr>
                                                    	<div class="col-xs-12">
                                                    	
                                                    		<?= $form->field($modelTransactionSessionOrder, 'delivery_method_id')->radioList(
                                                    		    ArrayHelper::map(
                                                    		        $modelTransactionSession['business']['businessDeliveries'],
                                                    		        'delivery_method_id',
                                                    		        function ($data) {
                                                    		            
                                                    		            return '
                                                                            <div class="row mb-10">
                                                                                <div class="col-sm-4 col-xs-12">
                                                                                    <label>' .
                                                                                        Html::radio('delivery_method_id', false, ['value' => $data['deliveryMethod']['id']]) . $data['deliveryMethod']['delivery_name'] .
                                                                                    '</label>
                                                		                        </div>
                                                                                <div class="col-sm-8 col-xs-12">
                                                                                    <strong>' . $data['note'] . '</strong>
                                                                                </div>
                                                                                <div class="' . (!empty($data['note']) ? 'col-sm-offset-4 ' : '') . 'col-sm-8 col-xs-12">' .
                                                                                    $data['description'] . '
                                                                                </div>
                                                                            </div>';
                                                    		        }
                                                		        ),
                                                		        [
                                                		            'item' => function ($index, $label, $name, $checked, $value) {
                                                		                
                                                    		            return $label;
                                                		            }
                                                		        ]) ?>
                                            		        
                                                    	</div>
                                                	</div>
                                                    	
                                                	<div class="row">
                                                		<h5 class="font-alt text-center"><?= Yii::t('app', 'Payment Methods') ?></h5><hr>
                                                    	<div class="col-xs-12">
                                                    		
                                                    		<?= $form->field($modelTransactionSessionOrder, 'payment_method_id')->radioList(
                                                    		    ArrayHelper::map(
                                                    		        $modelTransactionSession['business']['businessPayments'],
                                                    		        'payment_method_id',
                                                    		        function ($data) {
                                                    		            
                                                    		            return '
                                                                            <div class="row mb-10">
                                                                                <div class="col-sm-4 col-xs-12">
                                                                                    <label>' .
                                                                                        Html::radio('payment_method_id', false, ['value' => $data['paymentMethod']['id']]) . $data['paymentMethod']['payment_name'] .
                                                    		                        '</label>
                                                		                        </div>
                                                                                <div class="col-sm-8 col-xs-12">
                                                                                    <strong>' . $data['note'] . '</strong>
                                                                                </div>
                                                                                <div class="' . (!empty($data['note']) ? 'col-sm-offset-4 ' : '') . 'col-sm-8 col-xs-12">' .
                                                                                    $data['description'] . '
                                                                                </div>
                                                                            </div>';
                                                    		        }
                                                		        ),
                                                		        [
                                                		            'item' => function ($index, $label, $name, $checked, $value) {
                                                		                
                                                    		            return $label;
                                                		            }
                                                		        ]) ?>
                                            		        
                                                    	</div>
                                                	</div>
                                                    	
                                            	<?php
                                                endif; ?>
                                                
                                                <div class="<?= $hiddenClass ? 'hidden' : '' ?>" id="no-data">
                                                	<?= Yii::t('app', 'Your order list is empty') . '. ' . Yii::t('app', 'Please order the item you want first') ?>
                                                </div>
                                                
                                                <div class="row mt-70">
                                                	<div class="col-sm-5 col-sm-offset-7 col-xs-12">
                                                        <table class="table table-responsive table-striped table-border checkout-table">
                                                            <tbody>
                                                                <tr>
                                                                    <th class="font-alt">Total</th>
                                                                    <td class="total-price"><?= Yii::$app->formatter->asCurrency(!empty($modelTransactionSession['total_price']) ? $modelTransactionSession['total_price'] : 0) ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                
                                                        <?= Html::submitButton(Yii::t('app', 'Order Now'), ['class' => 'btn btn-d btn-round btn-block btn-submit', 'disabled' => empty($modelTransactionSession)]) ?>
    
                                                    </div>
                                                </div>
                							</div>
                						</div>
                						
                					<?php
                					ActiveForm::end(); ?>
            						
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

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $(".transaction-item-amount").on("change", function() {

        var thisObj = $(this);
        var amount = parseInt(thisObj.val());

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": thisObj.parents(".business-menu-group").find(".transaction-item-id").val(),
                "amount": amount
            },
            beforeSend: function(xhr) {

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {
                    
                    thisObj.parents(".business-menu-group").find(".transaction-item-amount").val(amount);
                    
                    $(".total-price").html(response.total_price);
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
        var notes = thisObj.val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": thisObj.parents(".business-menu-group").find(".transaction-item-id").val(),
                "note": notes
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-text").show();
            },
            success: function(response) {

                if (!response.success) {

                    messageResponse(response.icon, response.title, response.text, response.type);
                }

                thisObj.parents(".business-menu-group").find(".transaction-item-notes").val(notes);

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
                "id": $(this).parents(".business-menu-group").find(".transaction-item-id").val()
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.children().removeClass("fa-times").addClass("fa-spinner fa-spin");
            },
            success: function(response) {

                if (response.success) {
                    
                    thisObj.parents(".business-menu-group").remove();

                    if (!$(".business-menu-group").length) {
                        
                        $("#no-data").removeClass("hidden");
                        $(".btn-submit").prop("disabled", true);
                    }

                    $(".total-price").html(response.total_price);
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

if (!empty(($message = Yii::$app->session->getFlash('message')))) {
    
    $jscript = 'messageResponse("aicon aicon-icon-tick-in-circle", "' . $message['title'] . '" , "' . $message['message'] . '", "danger");';
}

$this->registerJs($jscript); ?>