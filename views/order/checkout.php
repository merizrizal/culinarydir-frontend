<?php

use yii\helpers\Html;
use yii\web\View;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $modelTransactionSession core\models\TransactionSession */

$this->title = 'Checkout'; ?>

<div class="main">

	<section class="module-extra-small bg-main">
		<div class="container detail checkout-order">
		
			<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back to Order List'), ['order/order-list']) ?>

                </div>
            </div>
		
			<div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            
            		<div class="row">
            			<div class="col-sm-12 col-xs-12">        					
            				<div class="box bg-white">
            					<div class="box-title">
            						<h4 class="font-alt text-center"><?= Yii::t('app', 'Order Detail') ?></h4>
            					</div>
            					
            					<hr class="divider-w">
            					
            					<div class="box-content">
            					
                					<?= Html::beginForm(['order/checkout', 'id' => $modelTransactionSession['id']], 'post') ?>
                					
                						<?= Html::hiddenInput('transaction_id', $modelTransactionSession['id']) ?>
                					
                						<div class="row">
                							<div class="col-xs-12">
                								
                								<?php                								
                								foreach ($modelTransactionSession['transactionItems'] as $dataTransactionItem): ?>
                								
                    								<div class="row mb-10">
                                                        <div class="col-xs-7">
                                                            <strong><?= $dataTransactionItem['businessProduct']['name'] ?></strong>
                                                        </div>
                                                        <div class="col-xs-5">
                                                            <strong style="white-space: pre"><?= $dataTransactionItem['amount'] . '  x  '. Yii::$app->formatter->asCurrency($dataTransactionItem['price']) ?></strong>
                                                        </div>
                                                        <div class="col-xs-12">
                                                            <p class="mb-0"><?= $dataTransactionItem['note'] ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                <?php
                                                endforeach; ?>
                                                
                                                <div class="row mt-70">
                                                	<div class="col-sm-5 col-sm-offset-7 col-xs-12">
                                                        <table class="table table-responsive table-striped table-border checkout-table">
                                                            <tbody>
                                                                <tr>
                                                                    <th class="font-alt">Total</th>
                                                                    <td><?= Yii::$app->formatter->asCurrency($modelTransactionSession['total_price']) ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                
                                                        <?= Html::submitButton(Yii::t('app', 'Order Now'), ['class' => 'btn btn-d btn-round btn-block']) ?>
    
                                                    </div>
                                                </div>
                							</div>
                						</div>
                						
                					<?= Html::endForm() ?>
            						
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

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '';

if (!empty(($message = Yii::$app->session->getFlash('message')))) {
    
    $jscript = 'messageResponse("aicon aicon-icon-tick-in-circle", "' . $message['title'] . '" , "' . $message['message'] . '", "danger");';
}

$this->registerJs($jscript); ?>