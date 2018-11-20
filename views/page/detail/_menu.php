<?php

use yii\helpers\Html;

/* @var $modelBusinessProduct core\models\BusinessProduct */ ?>

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
                                    <div class="col-md-8 col-xs-8">
                                        <strong><?= $dataBusinessProduct['name'] ?></strong>
                                        <span style="display: block; width: 80%"></span>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                    </div>
                                </div>
                                <div class="row mb-20">
                                    <div class="col-md-7 col-xs-12">
                                        <p class="mb-0">
                                            <?= $dataBusinessProduct['description'] ?>
                                        </p>
                                    </div>
                                    <div class="col-md-offset-1 col-md-3 col-xs-offset-7 col-xs-5">
                                    
                                    	<?= Html::a('<i class="fa fa-plus"></i> Pesan Ini', null, [
                                    	    'class' => 'btn btn-success btn-round btn-xs'
                                    	]) ?>
                                    	
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