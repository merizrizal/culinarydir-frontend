<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\View;
use frontend\components\AddressType;

/* @var $this yii\web\View */
/* @var $modelBusinessPromo core\models\BusinessPromo */

common\assets\OwlCarouselAsset::register($this);

$this->title = $modelBusinessPromo['title'];

$ogUrl = [
    'page/detail-promo',
    'id' => $modelBusinessPromo['id'],
    'uniqueName' => $modelBusinessPromo['business']['unique_name']
];

$ogImage = Yii::$app->params['endPointLoadImage'] . 'business-promo?image=&w=490&h=276';

if (!empty($modelBusinessPromo['image'])) {
    
    $ogImage = Yii::$app->params['endPointLoadImage'] . 'business-promo?image=' . $modelBusinessPromo['image'];
}

$ogDescription = !empty($modelBusinessPromo['short_description']) ? $modelBusinessPromo['short_description'] : $this->title;

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => $ogDescription
]);

$this->registerMetaTag([
    'property' => 'og:url',
    'content' => Yii::$app->urlManager->createAbsoluteUrl($ogUrl)
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
    'content' => $ogDescription
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
                        'city' => Inflector::slug($modelBusinessPromo['business']['businessLocation']['city']['name']),
                        'uniqueName' => $modelBusinessPromo['business']['unique_name'],
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
                                            <div class="col-xs-12 text-center">
                                            	<div class="promo-image-container owl-carousel owl-theme">
                                                    <?= Html::img(null, ['class' => 'owl-lazy', 'data-src' => Yii::$app->params['endPointLoadImage'] . 'business-promo?image=' . $modelBusinessPromo['image']]); ?>
                                                </div>
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
                                            <h4 class="m-0"><?= $modelBusinessPromo['title']; ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <hr class="divider-w">
								
								<?php
								$promoRange = Yii::t('app', 'Valid from {dateStart} until {dateEnd}', [
								    'dateStart' => Yii::$app->formatter->asDate($modelBusinessPromo['date_start'], 'medium'), 
								    'dateEnd' => Yii::$app->formatter->asDate($modelBusinessPromo['date_end'], 'medium')
								]); ?>
								
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

<?php
$jscript = '
    $(".promo-image-container").owlCarousel({
        lazyLoad: true,
        items: 1,
        mouseDrag: false,
        touchDrag: false
    });
';

$this->registerJs($jscript);

$this->on(View::EVENT_END_BODY, function() use ($modelBusinessPromo, $ogImage) {

    echo '
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Event",
            "name": "' . $modelBusinessPromo['title'] . '",
            "startDate": "' . Yii::$app->formatter->asDate($modelBusinessPromo['date_start']) . '",
            "location": {
                "@type": "Place",
                "name": "' . $modelBusinessPromo['business']['name'] . '",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "' . AddressType::widget([
                        'businessLocation' => $modelBusinessPromo['business']['businessLocation'],
                        'showDetail' => true
                    ]). '",
                    "addressLocality": "' . $modelBusinessPromo['business']['businessLocation']['city']['name'] . '"
                }
            },
            "image": "' . $ogImage . '",
            "description": "' . $modelBusinessPromo['short_description'] . '",
            "endDate": "' . Yii::$app->formatter->asDate($modelBusinessPromo['date_end']) . '"
        }
        </script>
    ';
}); ?>