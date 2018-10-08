<?php

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
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-xs-9">
                                        <p>
                                            <?= $dataBusinessProduct['description'] ?>
                                        </p>
                                    </div>
                                </div>

                            <?php
                            endforeach;
                            
                        else: ?>
        
                        	<p><?= Yii::t('app', 'Currently there is no menu available') . '.' ?></p>
        
                        <?php
                        endif; ?>
                     
                	</div>
                </div>
            </div>
        </div>
    </div>
</div>