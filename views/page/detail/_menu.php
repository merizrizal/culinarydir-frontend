<?php 
/* @var $modelBusinessProduct core\models\BusinessProduct */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <h4 class="mt-0 mb-0 inline-block">Menu</h4>
            </div>

            <hr class="divider-w">

            <?php
            if (!empty($modelBusinessProduct)): ?>

                <div class="box-content">
                    <div class="row">

                        <?php
                        foreach ($modelBusinessProduct as $dataBusinessProduct): ?>

                            <div class="col-md-12 col-xs-12">
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
                            </div>

                        <?php
                        endforeach; ?>

                    </div>
                </div>

            <?php
            else: ?>

                <div class="box-content mt-10">
                    <p><?= Yii::t('app', 'Currently there is no menu available') . '.' ?></p>
                </div>

            <?php
            endif; ?>

        </div>
    </div>
</div>