<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\touchspin\TouchSpin;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */
/* @var $modelTransactionSessionOrder core\models\TransactionSessionOrder */
/* @var $promoItemClaimed Array */
/* @var $dataOption Array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$this->title = 'Checkout'; ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail checkout-order">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-xs-12">

                    <?php
                    if (!empty($modelTransactionSession)) {

                        echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Continue Ordering'), ['page/menu', 'uniqueName' => $modelTransactionSession['business']['unique_name']], ['class' => 'btn btn-standard p-0']);
                    } else {

                        echo Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Home Page'), ['page/index'], ['class' => 'btn btn-standard p-0']);
                    } ?>

                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-xs-12">

                    <div class="row">
                        <div class="col-xs-12">
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
                                    ]); ?>

                                        <div class="row">
                                            <div class="col-xs-12 order-list">
    
                                                <?php
                                                if (!empty($modelTransactionSession)):
    
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
                                                                <div class="row">
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
    
                                                    <?php
                                                    endforeach; ?>
    
                                                    <div class="row mt-40">
                                                        <div class ="promo-code-section">
                                                            <div class="col-xs-12">
                                                                <?= Yii::t('app', 'Got Promo Code?') ?>
                                                            </div>
                                                            <div class="col-sm-4 col-xs-12 mb-20">
    
                                                                <?= $form->field($modelTransactionSession, 'promo_item_id')->dropDownList(
                                                                    ArrayHelper::map($promoItemClaimed, 'id',
                                                                        function ($data) {
                                                                            
                                                                            return substr($data['id'], 0, 6);
                                                                        }
                                                                    ),
                                                                    [
                                                                        'prompt' => '',
                                                                        'class' => 'promo-code-field form-control',
                                                                        'options' => $dataOption
                                                                    ]); ?>
    
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-offset-3 col-sm-5 col-xs-12">
                                                            <table class="table table-responsive table-striped table-border checkout-table">
                                                                <tbody>
                                                                	<tr>
                                                                        <th class="font-alt">Total</th>
                                                                        <td class="total-price"><?= $modelTransactionSession['total_price'] ?></td>
                                                                    </tr>
                                                                    <tr class="promo-amount" style="display:none">
                                                                        <th class="font-alt">Promo</th>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr class="grand-total" style="display:none">
                                                                        <th class="font-alt">Grand Total</th>
                                                                        <td></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
    
                                                    <div class="order-online-form">
                                                        <div class="row mt-30">
                                                            <h5 class="font-alt text-center"><?= Yii::t('app', 'Delivery Methods') ?></h5>
                                                            <hr>
                                                            <div class="col-xs-12">
    
                                                                <?php
                                                                if (!empty($modelTransactionSession['business']['businessDeliveries'])) {
    
                                                                    echo $form->field($modelTransactionSessionOrder, 'business_delivery_id')->radioList(
                                                                        ArrayHelper::map(
                                                                            $modelTransactionSession['business']['businessDeliveries'],
                                                                            'id',
                                                                            function ($data) use ($form, $modelTransactionSessionOrder) {
    
                                                                                return '
                                                                                    <div class="row mb-20">
                                                                                        <div class="col-sm-4 col-xs-12">
                                                                                            <label>' .
                                                                                                $form->field($modelTransactionSessionOrder, 'business_delivery_id')->radio(['label' => $data['deliveryMethod']['delivery_name'], 'value' => $data['id'], 'uncheck' => null]) .
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
                                                                        ]);
                                                                } else {
    
                                                                    echo Yii::t('app', 'Currently there is no delivery method available in') . ' ' . $modelTransactionSession['business']['name'];
                                                                } ?>
    
                                                            </div>
                                                        </div>
    
                                                        <div class="row mt-30">
                                                            <h5 class="font-alt text-center"><?= Yii::t('app', 'Payment Methods') ?></h5>
                                                            <hr>
                                                            <div class="col-xs-12">
    
                                                                <?php
                                                                if (!empty($modelTransactionSession['business']['businessPayments'])) {
    
                                                                    echo $form->field($modelTransactionSessionOrder, 'business_payment_id')->radioList(
                                                                        ArrayHelper::map(
                                                                            $modelTransactionSession['business']['businessPayments'],
                                                                            'id',
                                                                            function ($data) use ($form, $modelTransactionSessionOrder) {
    
                                                                                return '
                                                                                    <div class="row mb-20">
                                                                                        <div class="col-sm-4 col-xs-12">
                                                                                            <label>' .
                                                                                                $form->field($modelTransactionSessionOrder, 'business_payment_id')->radio(['label' => $data['paymentMethod']['payment_name'], 'value' => $data['id'], 'uncheck' => null]) .
                                                                                            '</label>
                                                                                        </div>
                                                                                        <div class="col-sm-8 col-xs-12">' .
                                                                                            $data['description'] . '
                                                                                        </div>
                                                                                    </div>';
                                                                            }
                                                                        ),
                                                                        [
                                                                            'item' => function ($index, $label, $name, $checked, $value) {
    
                                                                                return $label;
                                                                            }
                                                                        ]);
    
                                                                    echo '<i>*' . Yii::t('app', 'For transfer or online payments, please send a screenshot of proof of payment') . '</i>';
                                                                } else {
    
                                                                    echo Yii::t('app', 'Currently there is no payment method available in') . ' ' . $modelTransactionSession['business']['name'];
                                                                } ?>
    
                                                            </div>
                                                        </div>
    
                                                        <div class="row mt-30">
                                                            <div class="col-xs-12">
                                                                <strong><?= Yii::t('app', 'Order Information') ?></strong>
                                                                <?= $form->field($modelTransactionSession, 'note')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Add Notes to Seller (Optional)')]) ?>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                <?php
                                                else:
    
                                                    echo Yii::t('app', 'Your order list is empty') . '. ' . Yii::t('app', 'Please order the item you want first'); ?>
    
                                                    <div class="row mt-40">
                                                        <div class="col-sm-offset-7 col-sm-5 col-xs-12">
                                                            <table class="table table-responsive table-striped table-border checkout-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="font-alt">Total</th>
                                                                        <td class="total-price">0</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
    
                                                <?php
                                                endif; ?>
    
                                                <div class="row">
                                                    <div class="col-sm-offset-7 col-sm-5 col-xs-12">
                                                        <?= Html::submitButton(Yii::t('app', 'Order Now'), ['class' => 'btn btn-d btn-round btn-block btn-order', 'disabled' => empty($modelTransactionSession)]) ?>
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
    
<div class="order-confirmation-modal" style="display:none">
	<div class="row">
		<div class="col-xs-12">
			<div class="modal-header-search">
    			<div class="row">
    				<div class="col-md-offset-4 col-sm-offset-3 col-sm-5 col-xs-offset-1 col-xs-10">
                        <div class="input-group">
                        	<div class="input-group-addon">
                        		<button type="button" class="close btn-close text-red"><i class="fas fa-arrow-left"></i></button>
                        	</div>
                        	<span class="modal-title-search">Order Summary</span>
                    	</div>
                	</div>
            	</div>
            </div>
		</div>
		<div class="col-md-7 col-md-offset-4 col-sm-offset-3 col-sm-8 col-tab-12 col-xs-offset-1 col-xs-11">
		
			<?php
			if (!empty($modelTransactionSession)):
			
    			foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem): 
    			
    			    $amountPrice = '<span class="item-amount">' . $dataTransactionItem['amount'] . '</span> x ' . Yii::$app->formatter->asCurrency($dataTransactionItem['price']); ?>
    				
    				<div id=item-<?= $dataTransactionItem['id'] ?>>
        				<div class="row">
        					<div class="col-sm-6 col-tab-7 col-xs-12">
        						<strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
        					</div>
        					<div class="col-sm-6 col-tab-5 visible-lg visible-md visible-sm visible-tab">
        						<strong><?= $amountPrice ?></strong>
        					</div>
        				</div>
        				
        				<div class="row mb-10">
        					<div class="col-xs-12 item-note">
        						<?= $dataTransactionItem['note'] ?>
        					</div>
        					<div class="col-xs-12 visible-xs">
        						<strong><?= $amountPrice ?></strong>
        					</div>
        				</div>
    				</div>
    			
    			<?php
    			endforeach;
			endif; ?>
			
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-7 col-md-offset-4 col-sm-offset-3 col-sm-8 col-tab-12 col-xs-offset-1 col-xs-11">
			<div class="row">
        		<div class="col-xs-11">
        			<div class="delivery-method"></div>
        		</div>
        	</div>
        	<div class="row">
        		<div class="col-xs-11">
        			<div class="payment-method"></div>
        		</div>
        	</div>
        	<div class="row" style="display:none">
        		<div class="col-xs-12">
        			<div class="promo-code"></div>
        		</div>
        	</div>
		</div>
	</div>
	
	<div class="row" style="display:none">
		<div class="col-md-7 col-md-offset-4 col-sm-offset-3 col-sm-8 col-tab-12 col-xs-offset-1 col-xs-11">
			<div class="transaction-note"></div>
		</div>
	</div>
	
	<div class="row mt-20">
		<div class="col-md-5 col-md-offset-4 col-sm-offset-3 col-sm-6 col-tab-10 col-xs-offset-1 col-xs-10">
    		<table class="table table-responsive table-striped table-border checkout-table">
                <tbody>
                	<tr>
                        <th class="font-alt">Total</th>
                        <td class="total-price"><?= $modelTransactionSession['total_price'] ?></td>
                    </tr>
                    <tr class="promo-amount-confirm" style="display:none">
                        <th class="font-alt">Promo</th>
                        <td></td>
                    </tr>
                    <tr class="grand-total-confirm" style="display:none">
                        <th class="font-alt">Grand Total</th>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <?= Html::button(Yii::t('app', 'Order Now'), ['class' => 'btn btn-d btn-round btn-block btn-submit-order']); ?>
            
        </div>
	</div>
</div>

<?php
GrowlCustom::widget();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/customicheck/customicheck.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/jquery-currency/jquery.currency.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    var totalPrice = ' . $modelTransactionSession['total_price'] . ';

    $(".total-price").currency({' . Yii::$app->params['currencyOptions'] . '});

    if ($(".promo-code-field").val() != "") {

        var amount = $(this).find(":selected").data("amount");

        var grandTotal = totalPrice - amount < 0 ? 0 : totalPrice - amount;
        
        $(".promo-amount, .promo-amount-confirm").show();
        $(".promo-amount").children().last().html(amount);
        $(".promo-amount-confirm").children().last().html(amount);
        $(".promo-amount").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
        $(".promo-amount-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});

        $(".grand-total, .grand-total-confirm").show();
        $(".grand-total").children().last().html(grandTotal);
        $(".grand-total-confirm").children().last().html(grandTotal);
        $(".grand-total").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
        $(".grand-total-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
    }

    $(".promo-code-field").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Promo Code') . '",
        minimumResultsForSearch: "Infinity"
    });

    $(".transaction-item-amount").on("change", function() {

        var thisObj = $(this);
        var amount = parseInt(thisObj.val());
        var transactionItemId = thisObj.parents(".business-menu-group").find(".transaction-item-id").val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": transactionItemId,
                "amount": amount
            },
            beforeSend: function(xhr) {

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    thisObj.parents(".business-menu-group").find(".transaction-item-amount").val(amount);
                    $("#item-" + transactionItemId).find(".item-amount").html(amount);

                    $(".total-price").html(response.total_price);
                    $(".total-price").currency({' . Yii::$app->params['currencyOptions'] . '});

                    totalPrice = response.total_price;

                    if ($(".promo-amount").is(":visible")) {

                        var grandTotal = totalPrice - $(".promo-code-field").find(":selected").data("amount");

                        $(".grand-total").children().last().html(grandTotal < 0 ? 0 : grandTotal);
                        $(".grand-total-confirm").children().last().html(grandTotal < 0 ? 0 : grandTotal);
                        $(".grand-total").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
                        $(".grand-total-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
                    }
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
        var transactionItemId = thisObj.parents(".business-menu-group").find(".transaction-item-id").val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "id": transactionItemId,
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
                $("#item-" + transactionItemId).find(".item-note").html(notes);

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
        var transactionItemId = thisObj.parents(".business-menu-group").find(".transaction-item-id").val();

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "id": transactionItemId
            },
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.children().removeClass("fa-times").addClass("fa-spinner fa-spin");
            },
            success: function(response) {

                if (response.success) {

                    thisObj.parents(".business-menu-group").remove();
                    $("#item-" + transactionItemId).remove();

                    if (!$(".business-menu-group").length) {

                        $(".promo-code-section").siblings().removeClass("col-sm-offset-3").addClass("col-sm-offset-7");
                        $(".promo-code-section").remove();
                        $(".order-online-form").remove();
                        $(".promo-amount").remove();
                        $(".grand-total").remove();
                        $(".order-list").prepend("' . Yii::t('app', 'Your order list is empty') . '. ' . Yii::t('app', 'Please order the item you want first') . '");
                        $(".btn-order").prop("disabled", true);
                    } else {

                        totalPrice = response.total_price;
    
                        if ($(".promo-amount").is(":visible")) {
    
                            var grandTotal = totalPrice - $(".promo-code-field").find(":selected").data("amount");
    
                            $(".grand-total").children().last().html(grandTotal < 0 ? 0 : grandTotal);
                            $(".grand-total-confirm").children().last().html(grandTotal < 0 ? 0 : grandTotal);
                            $(".grand-total").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
                            $(".grand-total-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
                        }
                    }

                    $(".total-price").html(response.total_price);
                    $(".total-price").currency({' . Yii::$app->params['currencyOptions'] . '});
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

    $(".promo-code-field").on("select2:select", function() {

        var amount = $(this).find(":selected").data("amount");

        var grandTotal = totalPrice - amount < 0 ? 0 : totalPrice - amount;
        
        $(".promo-amount, .promo-amount-confirm").show();
        $(".promo-amount").children().last().html(amount);
        $(".promo-amount-confirm").children().last().html(amount);
        $(".promo-amount").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
        $(".promo-amount-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});

        $(".grand-total, .grand-total-confirm").show();
        $(".grand-total").children().last().html(grandTotal);
        $(".grand-total-confirm").children().last().html(grandTotal);
        $(".grand-total").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
        $(".grand-total-confirm").children().last().currency({' . Yii::$app->params['currencyOptions'] . '});
    });

    $(".btn-order").on("click", function(event) {
        
        event.preventDefault();

        $(".order-confirmation-modal").fadeIn("medium");

        var deliveryMethod = $("input[name=\'TransactionSessionOrder[business_delivery_id]\']:checked").parent().parent().text();
        var paymentMethod = $("input[name=\'TransactionSessionOrder[business_payment_id]\']:checked").parent().parent().text();

        $(".order-confirmation-modal").find(".delivery-method").html("<strong>Metode Pengiriman : </strong>" + ((deliveryMethod == "") ? "<span class=\"text-red\">metode pengiriman tidak boleh kosong</span>" : deliveryMethod));
        $(".order-confirmation-modal").find(".payment-method").html("<strong>Metode Pembayaran : </strong>" + ((paymentMethod == "") ? "<span class=\"text-red\">metode pembayaran tidak boleh kosong</span>" : paymentMethod));
        
        if ($(".promo-code-field").find(":selected").text() != "") {

            $(".promo-code").parent().parent().show();
            $(".promo-code").html("<strong>Kode Promo : </strong>" + $(".promo-code-field").find(":selected").text());
        }

        if ($("#transactionsession-note").val() != "") {

            $(".transaction-note").parent().parent().show();
            $(".transaction-note").html("<strong>Catatan : </strong>" + $("#transactionsession-note").val());
        } else {

            $(".transaction-note").parent().parent().hide();
        }
    });

    $(".btn-submit-order").on("click", function() {
        
        $(".btn-order").submit();
    });

    $(".btn-close").on("click", function() {

        $(".order-confirmation-modal").fadeOut("medium");
    });
';

if (!empty(($message = Yii::$app->session->getFlash('message')))) {

    $jscript .= 'messageResponse("aicon aicon-icon-tick-in-circle", "' . $message['title'] . '" , "' . $message['message'] . '", "danger");';
}

$this->registerJs($jscript); ?>