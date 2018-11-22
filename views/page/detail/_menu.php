<?php

use yii\helpers\Html;
use core\models\TransactionSession;

/* @var $this yii\web\View */
/* @var $modelBusinessProduct core\models\BusinessProduct */ 

if (!Yii::$app->user->getIsGuest()) {
    
    $modelTransactionSession = TransactionSession::find()
        ->andWhere(['transaction_session.user_ordered' => Yii::$app->user->id])
        ->andWhere(['transaction_session.is_closed' => false])
        ->asArray()->one();
    
    echo Html::hiddenInput('sess_id', !empty($modelTransactionSession['id']) ? $modelTransactionSession['id'] : null, ['class' => 'sess-id']);
    echo Html::hiddenInput('total_price', !empty($modelTransactionSession['total_price']) ? $modelTransactionSession['total_price'] : 0, ['class' => 'total-price']);
} ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <h4 class="mt-0 mb-0 inline-block">Menu</h4>
            </div>

            <hr class="divider-w">

			<div class="box-content mt-10">
				<div class="row">
					<div class="col-md-12 col-xs-12">

                        <?php
                        if (!empty($modelBusinessProduct)):
                    
                            foreach ($modelBusinessProduct as $dataBusinessProduct): ?>

                                <div class="row">
                                    <div class="col-md-8 col-xs-7">
                                        <strong><?= $dataBusinessProduct['name'] ?></strong>
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
                                    
                                    	<?= Html::a('<i class="fa fa-plus"></i> Pesan Ini', ['order-action/save-order'], [
                                    	    'class' => 'btn btn-success btn-round btn-xs add-to-cart'
                                    	]) ?>
                                    	
                                    	<?= Html::hiddenInput('nama_menu', $dataBusinessProduct['name'], ['class' => 'menu-name']) ?>
                                        <?= Html::hiddenInput('menu_id', $dataBusinessProduct['id'], ['class' => 'menu-id']) ?>
                                        <?= Html::hiddenInput('harga_satuan', $dataBusinessProduct['price'], ['class' => 'price']) ?>
                                    	
                                	</div>
                                </div>

                            <?php
                            endforeach;
                            
                        else: ?>
        
                        	<p>
                        		<?= Yii::t('app', 'Currently there is no menu available') . '.' ?>
                    		</p>
        
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
        var menuName = thisObj.siblings(".menu-name").val();
        var totalPrice = parseInt($(".total-price").val()) + parseInt(thisObj.siblings(".price").val());

        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "sess_id": $(".sess-id").val(),
                "total_price": totalPrice,
                "menu_id": thisObj.siblings(".menu-id").val(),
                "price": thisObj.siblings(".price").val()
            },
            beforeSend: function(xhr) {

                thisObj.find("i").removeClass("fa fa-plus").addClass("icon-hourglass");
            },
            success: function(response) {

                if (response.success) {

                    $(".sess-id").val(response.sess_id);
                    $(".total-price").val(totalPrice);
                }

                thisObj.find("i").removeClass("icon-hourglass").addClass("fa fa-plus");
                messageResponse(response.message.icon, response.message.title, response.message.text.replace("<product>", menuName), response.message.type);
            },
            error: function (xhr, ajaxOptions, thrownError) {

                thisObj.find("span").removeClass("icon-hourglass").addClass("fa fa-plus");
                messageResponse("fa fa-warning", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>
