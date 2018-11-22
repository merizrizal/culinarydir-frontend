<?php

use yii\helpers\Html;
use kartik\touchspin\TouchSpin;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Order List'); ?>

<div class="main">
    <section class="module">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <h1 class="module-title font-alt"><?= Yii::t('app', 'Order List') ?></h1>
                </div>
            </div>
            
            <hr class="divider-w pt-20">
            
            <?php
            $noDataClass = 'hidden';
            $urlCheckout = null;

            $jumlahHarga = 0;

            $subtotal = 50000;
            $jumlahHarga += $subtotal; ?>
            
            <div class=<?= $noDataClass ?> id ="no-data">
            
            	<?= Yii::t('app', 'Your order list is empty') ?>
            	
            </div>
		
    		<div class="list-order hidden-xs">
            	<div class="row">
                	<div class="col-sm-3">
                		<h5 class="product-title">Ayam Goreng Kalasan</h5>
            		</div>
            		<div class="col-sm-2 subtotal">
            			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($subtotal) ?></h5>
            		</div>
            		<div class="col-md-2 col-sm-3">
        
                        <?= TouchSpin::widget([
                            'name' => 'jumlah-item',
                            'value' => 1,
                            'options' => [
                                'class' => 'jumlah-item text-right input-sm',
                                //'data-url' => Yii::$app->urlManager->createUrl(['action/change-qty', 'id' => $dataTransactionItem['id']])
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
            		<div class="col-md-3 col-sm-2 text-right">
            		
            			<?= Html::a('<i class="fa fa-times fa-2x"></i>', ['action/remove-item'], ['class' => 'remove-item']) ?>
            			
            		</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-sm-8">
        			
        				<?= Html::textInput('menu_notes', null, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
        				
        			</div>
        		</div>
    		</div>
    		
    		
    		<div class="list-order visible-xs">
            	<div class="row">
                	<div class="col-xs-9">
                		<h5 class="product-title">Ayam Goreng Kalasan</h5>
            		</div>
            		<div class="col-xs-3 text-right">
            		
            			<?= Html::a('<i class="fa fa-times fa-2x"></i>', ['action/remove-item'], ['class' => 'remove-item']) ?>
            			
            		</div>
            		<div class="col-xs-12">
            		
						<?= Html::textInput('menu_notes', null, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
						
        			</div>
        		</div>
        		
        		<br>
        		
        		<div class="row">
        			<div class="col-xs-12 subtotal text-right">
            			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($subtotal) ?></h5>
            		</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-xs-offset-5 col-xs-7">
        
                        <?= TouchSpin::widget([
                            'name' => 'jumlah-item',
                            'value' => 1,
                            'options' => [
                                'class' => 'jumlah-item text-right input-sm',
                                //'data-url' => Yii::$app->urlManager->createUrl(['action/change-qty', 'id' => $dataTransactionItem['id']])
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
    		
    		
    		<div class="list-order visible-tab">
            	<div class="row">
                	<div class="col-tab-3">
                		<h5 class="product-title">Ayam Goreng Kalasan</h5>
            		</div>
            		<div class="col-tab-2 subtotal">
            			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($subtotal) ?></h5>
            		</div>
            		<div class="col-tab-3">
        
                        <?= TouchSpin::widget([
                            'name' => 'jumlah-item',
                            'value' => 1,
                            'options' => [
                                'class' => 'jumlah-item text-right input-sm',
                                //'data-url' => Yii::$app->urlManager->createUrl(['action/change-qty', 'id' => $dataTransactionItem['id']])
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
            		<div class="col-tab-2 subtotal">
            			<h5 class="product-title"><?= Yii::$app->formatter->asCurrency($subtotal) ?></h5>
            		</div>
            		<div class="col-tab-2 text-right">
            		
            			<?= Html::a('<i class="fa fa-times fa-1x"></i>', ['action/remove-item'], ['class' => 'remove-item']) ?>
            			
            		</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-tab-12">

						<?= Html::textInput('menu_notes', null, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Note')]); ?>
						
        			</div>
        		</div>
    		</div>

            <hr>
            
            <div class="row mt-70">
                <div class="col-sm-5 col-sm-offset-7">
                    <div class="shop-Cart-totalbox">
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
    </section>
</div>

<?php
frontend\components\GrowlCustom::widget();

$jscript = '
    $(".jumlah-item").on("change", function() {

        var thisObj = $(this);
        var jumlah = parseInt(thisObj.val());

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "jumlah": jumlah
            },
            beforeSend: function(xhr) {

                thisObj.parent().siblings(".overlay").show();
                thisObj.parent().siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    $("#jumlah-harga").html(response.jumlah_harga);
                    thisObj.parents(".list-order").find(".subtotal").children().html(response.subtotal);
                } else {

                    messageResponse(response.message.icon, response.message.title, response.message.text, response.message.type);
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

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-text").show();
            },
            success: function(response) {

                if (response.success) {

                    thisObj.parents(".list-order").fadeOut(100, function() {

                        var rootObj = $(this).parent();
                        $(this).remove();

                        if (parentObj.children(".list-order").length < 1) {

                            parentObj.children("#no-data").removeClass("hidden");
                        }
                    });

                    $("#jumlah-harga").html(response.jumlah_harga);
                } else {

                    messageResponse(response.message.icon, response.message.title, response.message.text, response.message.type);
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
';

$this->registerJs($jscript); ?>