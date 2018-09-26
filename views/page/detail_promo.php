<?php

use yii\helpers\Html; ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail place-detail">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back'), [
                        'page/detail',
                        'id' => $modelBusinessPromo['business_id'],
                        '#' => 'special',
                    ]) ?>

                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row mb-20">
                        <div class="col-md-12 col-sm-12 col-xs-12">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="view">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#photo" aria-controls="photo" role="tab" data-toggle="tab"><i class="aicon aicon-camera"></i> Foto</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content box bg-white">
                                            <div role="tabpanel" class="tab-pane fade in active" id="photo">
                                                <div class="row">
                                                    <div class="col-sm-10 col-sm-offset-1">

                                                        <?php
                                                        if (!empty($modelBusinessPromo['image'])): ?>

                                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . '/img/business_promo/' . $modelBusinessPromo['image']); ?>

                                                        <?php
                                                        else: ?>

                                                            <div class="titan-caption">
                                                                <div class="caption-content">
                                                                    <div class="font-alt titan-title-size-2">Foto tidak tersedia</div>
                                                                </div>
                                                            </div>

                                                        <?php
                                                        endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="box bg-white">
                                        <div class="box-title">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-tab-12 col-xs-12">
                                                    <h4 class="font-alt m-0"><?= $modelBusinessPromo['title']; ?></h4>
                                                </div>

                                                <div class="visible-xs col-xs-12 clearfix"></div>
                                            </div>
                                        </div>

                                        <hr class="divider-w">

                                        <div class="box-content">
                                            <div class="row mt-0">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-tab-12 col-xs-12 col">
                                                    <h4 class="visible-lg visible-md visible-sm m-0"><small><?= 'Berlaku ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_start'], 'medium') . ' s/d ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_end'], 'medium'); ?> </small></h4>
                                                    <h4 class="visible-tab m-0"><small><?= 'Berlaku ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_start'], 'medium') . ' s/d ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_end'], 'medium'); ?></small></h4>
                                                    <small class="visible-xs mt-10"><?= 'Berlaku ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_start'], 'medium') . ' s/d ' . Yii::$app->formatter->asDate($modelBusinessPromo['date_end'], 'medium'); ?></small>
                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-xs-12">

                                                            <?= $modelBusinessPromo['description'] ?>

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
            </div>

        </div>
    </section>

</div>