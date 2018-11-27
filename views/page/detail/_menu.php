<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelBusinessProduct core\models\BusinessProduct */ ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box bg-white">
            <div class="box-title" id="title-menu">
                <h4 class="mt-0 mb-0 inline-block">Menu</h4>
            </div>

            <hr class="divider-w">

			<div class="box-content mt-10">
				<div class="row">
					<div class="col-xs-12">
					
						<div class="overlay" style="display: none;"></div>
    					<div class="loading-img" style="display: none;"></div>

                        <?php
                        if (!empty($modelBusinessProduct)):
                    
                            foreach ($modelBusinessProduct as $dataBusinessProduct): ?>
								
								<div class="business-menu">
                                    <div class="row">
                                        <div class="col-md-8 col-xs-7">
                                            <strong class="menu-name"><?= $dataBusinessProduct['name'] ?></strong>
                                            <span style="display: block; width: 80%"></span>
                                        </div>
                                        <div class="col-md-4 col-xs-5">
                                            <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                        </div>
                                    </div>
                                    <div class="row mb-20">
                                        <div class="col-md-8 col-xs-12">
                                            <p class="mb-0">
                                                <?= $dataBusinessProduct['description'] ?>
                                            </p>
                                        </div>
                                        <div class="col-md-offset-0 col-md-4 col-xs-offset-7 col-xs-5">
                
                                        	<?php
                                        	echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Order This'), ['order-action/save-order'], [
                                        	    'class' => 'btn btn-d btn-round btn-xs add-to-cart'
                                        	]);
                                        	
                                            echo Html::hiddenInput('menu_id', $dataBusinessProduct['id'], ['class' => 'menu-id']);
                                            echo Html::hiddenInput('price', $dataBusinessProduct['price'], ['class' => 'price']);
                                            echo Html::hiddenInput('business_id', $dataBusinessProduct['business_id'], ['class' => 'business-id']); ?>
                                        	
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
    $(".add-to-cart").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "menu_id": thisObj.siblings(".menu-id").val(),
                "price": thisObj.siblings(".price").val(),
                "business_id": thisObj.siblings(".business-id").val()
            },
            beforeSend: function(xhr) {

                thisObj.parents(".box-content").find(".overlay").show();
                thisObj.parents(".box-content").find(".loading-img").show();
            },
            success: function(response) {
                
                thisObj.parents(".box-content").find(".overlay").hide();
                thisObj.parents(".box-content").find(".loading-img").hide();

                messageResponse(response.message.icon, response.message.title, response.message.text.replace("<product>", thisObj.parents(".business-menu").find(".menu-name").html()), response.message.type);
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