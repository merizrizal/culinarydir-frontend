<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelBusinessProduct core\models\BusinessProduct */
/* @var $modelTransactionSession core\models\TransactionSession */ ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box bg-white">
            <div class="box-title" id="title-menu">
                <h4 class="mt-0 mb-0 inline-block"><?= Yii::t('app', 'Product')?></h4>
            </div>

            <hr class="divider-w">

			<div class="box-content mt-10">
				<div class="row">
					<div class="col-xs-12">
					
						<div class="overlay" style="display: none;"></div>
    					<div class="loading-img" style="display: none;"></div>
    					
    					<?php
    					echo Html::hiddenInput('session_id', $modelTransactionSession['id'], ['class' => 'session-id']);
    					echo Html::hiddenInput('total_price', Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']), ['class' => 'total-price']);
    					echo Html::hiddenInput('total_amount', $modelTransactionSession['total_amount'], ['class' => 'total-amount']);
    					echo Html::hiddenInput('business_name', $modelTransactionSession['business']['name'], ['class' => 'place-name']);
    					
                        if (!empty($modelBusinessProduct)):
                    
                            foreach ($modelBusinessProduct as $dataBusinessProduct): ?>
								
								<div class="business-menu">
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-7">
                                            <strong class="menu-name"><?= $dataBusinessProduct['name'] ?></strong>
                                        </div>
                                        <div class="col-sm-4 col-xs-5">
                                            <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                        </div>
                                    </div>
                                    <div class="row mb-20">
                                        <div class="col-sm-8 col-xs-7">
                                            <p class="mb-0">
                                                <?= $dataBusinessProduct['description'] ?>
                                            </p>
                                        </div>
                                        <div class="col-sm-4 col-xs-5">
                
                                        	<?php
                                        	echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Order This'), ['order-action/save-order'], [
                                        	    'class' => 'btn btn-d btn-round btn-xs add-to-cart'
                                        	]);
                                        	
                                            echo Html::hiddenInput('menu_id', $dataBusinessProduct['id'], ['class' => 'menu-id']);
                                            echo Html::hiddenInput('price', $dataBusinessProduct['price'], ['class' => 'price']); ?>
                                        	
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

<?php
$jscript = '
    var cart = null;

    if ($(".session-id").val() != "") {

        cart = stickyGrowl(
            "aicon aicon-icon-online-ordering aicon-1x",
            $(".total-amount").val() + " menu" + (($(".total-amount").val() > 1) ? "s" : "") + " | Total : " + $(".total-price").val(),
            $(".place-name").val(),
            "info"
        );
    }

    $(".add-to-cart").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "menu_id": thisObj.siblings(".menu-id").val(),
                "price": thisObj.siblings(".price").val(),
                "business_id": $(".business-id").val()
            },
            beforeSend: function(xhr) {

                thisObj.parents(".box-content").find(".overlay").show();
                thisObj.parents(".box-content").find(".loading-img").show();
            },
            success: function(response) {
                
                thisObj.parents(".box-content").find(".overlay").hide();
                thisObj.parents(".box-content").find(".loading-img").hide();
                
                if (response.success) {
                
                    if (cart != null) {
    
                        cart.update("title", "<b>" + response.total_amount + " menu" + ((response.total_amount > 1) ? "s" : "") + " | Total : " + response.total_price + "</b>");
                    } else {
    
                        cart = stickyGrowl(
                            "aicon aicon-icon-online-ordering aicon-1x", 
                            "<b>" + response.total_amount + " menu | Total : " + response.total_price + "</b>", 
                            response.place_name, 
                            "info"
                        );
                    }
                }

                messageResponse(response.icon, response.title, response.text.replace("<product>", thisObj.parents(".business-menu").find(".menu-name").html()), response.type);
            },
            error: function (xhr, ajaxOptions, thrownError) {

                thisObj.parents(".box-content").find(".overlay").hide();
                thisObj.parents(".box-content").find(".loading-img").hide();

                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>