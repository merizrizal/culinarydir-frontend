<?php

use yii\helpers\Html;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $modelBusinessPromo core\models\BusinessPromo */

$this->title = $modelBusinessPromo['title'];

$ogImage = Yii::$app->urlManager->getHostInfo() . Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 490, 276);

if (!empty($modelBusinessPromo['image'])) {
    
    $ogImage = Yii::$app->urlManager->getHostInfo() . Yii::getAlias('@uploadsUrl') . '/img/business_promo/' . $modelBusinessPromo['image'];
}

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => $modelBusinessPromo['short_description']
]);

$this->registerMetaTag([
    'property' => 'og:url',
    'content' => Yii::$app->urlManager->createAbsoluteUrl(['page/detail-promo', 'id' => $modelBusinessPromo['id']])
]);

$this->registerMetaTag([
    'property' => 'og:type',
    'content' => 'website'
]);

$this->registerMetaTag([
    'property' => 'og:title',
    'content' => !empty($modelBusinessPromo['title']) ? $modelBusinessPromo['title'] : 'Promo di Asikmakan'
]);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' => !empty($modelBusinessPromo['short_description']) ? $modelBusinessPromo['short_description'] : 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$this->registerMetaTag([
    'property' => 'og:image',
    'content' => $ogImage
]); ?>

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

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="view">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#photo" aria-controls="photo" role="tab" data-toggle="tab"><i class="aicon aicon-camera"></i> <?= Yii::t('app', 'Photo') ?></a>
                                    </li>
                                </ul>

                                <div class="tab-content box bg-white">
                                    <div role="tabpanel" class="tab-pane fade in active" id="photo">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12 text-center">

                                                <?php
                                                $img = Yii::getAlias('@uploadsUrl') . '/img/image-no-available.jpg';
                                                
                                                if (!empty($modelBusinessPromo['image'])) {

                                                    $img = Yii::getAlias('@uploadsUrl') . '/img/business_promo/' . $modelBusinessPromo['image'];
                                                } 
                                                
                                                echo Html::img($img); ?>

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
                                    </div>
                                </div>

                                <hr class="divider-w">
								
								<?php
								$dateStart = Yii::$app->formatter->asDate($modelBusinessPromo['date_start'], 'medium'); 
								$dateEnd = Yii::$app->formatter->asDate($modelBusinessPromo['date_end'], 'medium');
								
								$promoRange = Yii::t('app', 'Valid from {dateStart} until {dateEnd}', ['dateStart' => $dateStart, 'dateEnd' => $dateEnd]); ?>
								
                                <div class="box-content">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h4 class="visible-lg visible-md visible-sm visible-tab m-0"><small><?= $promoRange ?></small></h4>
                                            <small class="visible-xs"><?= $promoRange ?></small>
                                        </div>
                                    </div>
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
    </section>

</div>