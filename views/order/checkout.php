<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = 'Checkout'; ?>

<div class="main">

	<section class="module-extra-small bg-main">
		<div class="container detail checkout-order">
		
			<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            
            		<div class="row">
            			<div class="col-sm-12 col-xs-12">        					
            				<div class="box bg-white">
            					<div class="box-title">
            						<h4 class="font-alt text-center">Detail Pemesanan</h4>
            					</div>
            					
            					<hr class="divider-w">
            					
            					<div class="box-content">
            					
            						<div class="overlay" style="display: none;"></div>
    								<div class="loading-img" style="display: none;"></div>
            					
            						<?= Html::hiddenInput('transaction_id', $modelTransactionSession['id'], ['class' => 'transaction-id'])?>
            					
            						<div class="row">
            							<div class="col-xs-12">
            								
            								<?php                								
            								foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem): ?>
            								
                								<div class="row mt-10 item">
                                                    <div class="col-md-9 col-xs-7 item-name">
                                                        <strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
                                                    </div>
                                                    <div class="col-md-3 col-xs-5 text-right">
                                                        <strong><?= Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></strong>
                                                    </div>
                                                    <div class="col-md-9 col-xs-12">
                                                        <p class="mb-0"><?= $dataTransactionItem['note'] ?></p>
                                                    </div>
                                                    <div class="col-md-offset-0 col-md-3 col-xs-offset-7 col-xs-5 text-right">
                                                    	<strong>Qty :</strong> <span class="item-qty"><?= $dataTransactionItem['amount'] ?></span>
                                                	</div>
                                                </div>
                                                
                                            <?php
                                            endforeach; ?>
                                            
                                            <div class="row mt-10">
                                            	<div class="col-sm-5 col-sm-offset-7">
                                                    <h4 class="font-alt"><?= Yii::t('app', 'Total Order') ?></h4>
                                                    <table class="table table-responsive table-striped table-border checkout-table">
                                                        <tbody>
                                                            <tr>
                                                                <th>Total (Estimasi)</th>
                                                                <td id="total-price"><?= Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                            
                                                    <?= Html::button('Pesan Sekarang', [
                                            		    'class' => 'btn btn-d btn-round btn-block btn-submit-order',
                                            		    'data-url' => Yii::$app->urlManager->createUrl(['order/checkout', 'id' => $modelTransactionSession['id']])
                                            		]) ?>

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
		
		</div>
	</section>

</div>

<?php

$jscript = '
    $(".btn-submit-order").on("click", function() {
        
        if ($("#person-address").val() == "") return false; 

        var thisObj = $(this);
        var message = "Order:%0A";
        var itemsLength = $(".item").length;

        $(".item").each(function(index, element) {
            index++;
            message += $(this).find(".item-name").text().trim() + ": " + $(this).find(".item-qty").text().trim();
            if (itemsLength !== index) message += "%0A";
        });

        message = message.replace(/ /g, "%20");

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.data("url"),
            data: {
                "transaction_id": $(".transaction-id").val(),
                "message": message
            },
            beforeSend: function(xhr) {

                thisObj.parents(".box-content").find(".overlay").show();
                thisObj.parents(".box-content").find(".loading-img").show();
            },
            success: function(response) {

                thisObj.parents(".box-content").find(".overlay").hide();
                thisObj.parents(".box-content").find(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");

                thisObj.parents(".box-content").find(".overlay").hide();
                thisObj.parents(".box-content").find(".loading-img").hide();
            }
        });
    });
    
    function getWhastappLink(personPhone, yourMessage) {
        number = personPhone.replace(/-/g, "").replace("0", "+62");
        
        
        return console.log("https://api.whatsapp.com/send?phone=" + number + "&text=%20" + message);
    }
';

$this->registerJs($jscript) ?>