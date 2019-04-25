<?php

use frontend\components\GrowlCustom;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $modelPromo core\models\BusinessPromo */
/* @var $claimInfo String */
/* @var $countUserClaimed Integer */

common\assets\OwlCarouselAsset::register($this);

$this->title = $modelPromo['title'];

$ogTitle = $modelPromo['title'] . ' ' . $modelPromo['type'] . ' sebesar ' . Yii::$app->formatter->asCurrency($modelPromo['amount']);

$ogUrl = Yii::$app->urlManager->createAbsoluteUrl([
    'page/detail-promo',
    'id' => $modelPromo['id'],
]);

$ogImage = Yii::$app->params['endPointLoadImage'] . 'promo?image=&w=490&h=276';

if (!empty($modelPromo['image'])) {

    $ogImage = Yii::$app->params['endPointLoadImage'] . 'promo?image=' . $modelPromo['image'];
}

$ogDescription = $this->title;

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
    'content' => !empty($modelPromo['title']) ? $modelPromo['title'] : 'Promo di Asikmakan'
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
                <div class="col-md-10 col-md-offset-1 col-xs-12">
                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back To Home Page'), ['page/index']) ?>
                </div>
            </div>

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<div class="view">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#photo" aria-controls="photo" role="tab" data-toggle="tab"><i class="aicon aicon-camera1"></i> <?= Yii::t('app', 'Photo') ?></a>
                                    </li>
                                </ul>
                                <div class="tab-content box bg-white">
                                    <div role="tabpanel" class="tab-pane fade in active" id="photo">
                                        <div class="row">
                                            <div class="col-xs-12 text-center">
                                            	<div class="promo-image-container owl-carousel owl-theme">
                                                    <?= Html::img(null, ['class' => 'owl-lazy', 'data-src' => Yii::$app->params['endPointLoadImage'] . 'promo?image=' . $modelPromo['image']]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>

                    <div class="row mt-20">
                        <div class="col-xs-12">
                            <div class="box bg-white">
                                <div class="box-title">
                                    <div class="row mt-10">
                                        <div class="col-xs-8">
                                        	<h4 class="mt-0 mb-0"><?= $modelPromo['title']; ?></h4>
                                        </div>
                                        <div class="col-sm-4 col-tab-4 text-right visible-lg visible-md visible-sm visible-tab">
                                        	<?= Html::button('<i class="fa fa-share-alt"></i> Share', ['class' => 'btn btn-default btn-standard btn-round-4 share-promo']); ?>
                                        </div>
                                        <div class="col-xs-4 text-right visible-xs">
                                        	<?= Html::button('<i class="fa fa-share-alt"></i> Share', ['class' => 'btn btn-default btn-standard btn-small btn-round-4 share-promo']); ?>
                                        </div>
                                    </div>
                                </div>

                                <hr class="divider-w">

                                <div class="box-content">
                                	<div class="overlay" style="display:none"></div>
                                	<div class="loading-img" style="display:none"></div>

                                	<div class="row mb-10">
                                		<div class="col-xs-12">
                                			<i class="fas fa-tag"></i> <?= $modelPromo['type'] ?>
                                			<br>
                                			<i class="aicon aicon-rupiah"></i> <?= Yii::$app->formatter->asCurrency($modelPromo['amount']) ?>
                                			<br>
                                			<i class="aicon aicon-clock"></i>
                                			<strong>

                                    			<?= Yii::t('app', 'Valid from {dateStart} until {dateEnd}', [
                                                    'dateStart' => Yii::$app->formatter->asDate($modelPromo['date_start'], 'medium'),
                                                    'dateEnd' => Yii::$app->formatter->asDate($modelPromo['date_end'], 'medium')
                                                ]); ?>

                                            </strong>
                                            <br>
                                            <i class="fas fa-check"></i><span class="claim-info"> <?= $claimInfo ?></span>
                                		</div>
                                	</div>

                                	<hr class="divider-w">

                                    <div class="row mt-10">
                                        <div class="col-xs-12">
                                            <?= $modelPromo['description'] ?>
                                        </div>
                                    </div>

                                    <?= Html::a('Claim Promo', ['action/claim-promo'], [
                                        'class' => 'btn btn-block btn-round btn-d claim-promo-btn',
                                        'data-promo' => $modelPromo['id']
                                    ]) ?>

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

frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $(".promo-image-container").owlCarousel({
        lazyLoad: true,
        items: 1,
        mouseDrag: false,
        touchDrag: false
    });

    $(".claim-promo-btn").on("click", function() {

        var thisObj = $(this);
        var countUserClaimed = ' . $countUserClaimed . ';

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "promo_id": thisObj.data("promo")
            },
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {

                thisObj.siblings(".overlay").show();
                thisObj.siblings(".loading-img").show();
            },
            success: function(response) {

                if (response.success) {

                    if (countUserClaimed == 0) {

                        $(".claim-info").html(" ' . Yii::t('app', 'You have claimed this promo') . '");
                    } else {

                        $(".claim-info").html(" ' . Yii::t('app', 'You and {userClaimed} other user have claimed this promo', ['userClaimed' => $countUserClaimed]) . '");
                    }
                }

                messageResponse(response.icon, response.title, response.message, response.type);

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            }
        });

        return false;
    });

    $(".share-promo").on("click", function() {

        facebookShare({
            ogUrl: "' . $ogUrl . '",
            ogTitle: "' . $ogTitle . '",
            ogDescription: "' . addslashes($ogDescription) . '",
            ogImage: "' . $ogImage . '",
            type: "Promo"
        });

        return false;
    });
';

$this->registerJs($jscript); ?>