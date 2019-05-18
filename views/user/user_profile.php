<?php

use frontend\components\GrowlCustom;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $modelUser core\models\User */
/* @var $queryParams array */

$this->title = $modelUser['full_name'];

$ogDescription = $modelUser['full_name'] . ' telah bergabung Asikmakan.com sejak ' . \Yii::$app->formatter->asDate($modelUser['created_at'], 'long');

if (!empty($modelUser['userPerson']['person']['about_me'])) {

    $ogDescription = $modelUser['userPerson']['person']['about_me'];
}

$ogDescription = StringHelper::truncate(preg_replace('/[\r\n]+/','' , $ogDescription), 300);
$ogImage = \Yii::$app->params['endPointLoadImage'] . 'user?image=default-avatar.png';

if (!empty($modelUser['image'])) {

    $ogImage = \Yii::$app->params['endPointLoadImage'] . 'user?image=' . $modelUser['image'];
}

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
    'content' => \Yii::$app->urlManager->createAbsoluteUrl(['user/user-profile', 'user' => $modelUser['username']])
]);

$this->registerMetaTag([
    'property' => 'og:type',
    'content' => 'website'
]);

$this->registerMetaTag([
    'property' => 'og:title',
    'content' => $modelUser['full_name']
]);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' =>  $ogDescription
]);

$this->registerMetaTag([
    'property' => 'og:image',
    'content' => $ogImage
]); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">

            <div class="row user-profile-header mb-50">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                	<?php
                	$img = !empty($modelUser['image']) ? $modelUser['image'] . '&w=200&h=200' : 'default-avatar.png';
                	$userName = '<h3>' . $modelUser['full_name'] . ' </h3>'; ?>

                    <div class="row mt-10 visible-lg visible-md visible-sm visible-tab">
                        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-tab-8 col-xs-offset-2">
                            <div class="row ">
                                <div class="widget">
                                    <div class="widget-posts-image">
                                        <?= Html::img(\Yii::$app->params['endPointLoadImage'] . 'user?image=' . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']) ?>
                                    </div>
                                    <div class="widget-posts-body user-profile-identity">
                                        <?= $userName ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row visible-xs">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                	<?= Html::img(\Yii::$app->params['endPointLoadImage'] . 'user?image=' . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component center-block']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <?= $userName ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="view">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs widget mb-10" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#view-journey" aria-controls="view-journey" role="tab" data-toggle="tab">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <i class="aicon aicon-icon-been-there-fill-1 aicon-1-5x"></i>
                                                <li>Journey</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#view-photo" aria-controls="view-photo" role="tab" data-toggle="tab" id="nav-tabs-photo">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <i class="aicon aicon-camera1 aicon-1-5x"></i>
                                                <li><?= \Yii::t('app', 'Photo') ?></li>
                                                <span class="badge total-user-photo"></span>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active p-0" id="view-journey">
                                <?= $this->render('user/_journey', [
                                    'username' => $modelUser['username'],
                                    'queryParams' => $queryParams,
                                ]) ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-photo">
                                <?= $this->render('user/_photo', [
                                    'username' => $modelUser['username'],
                                    'queryParams' => $queryParams,
                                ]) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\Readmore::widget();
frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$this->on(View::EVENT_END_BODY, function() use ($modelUser, $ogDescription, $ogImage) {

    echo '
        <script type="application/ld+json">
        {
            "@context": "http://schema.org/",
            "@type": "Person",
            "name": "' . $modelUser['full_name'] . '",
            "image": "' . $ogImage . '"
        }
        </script>
    ';
}); ?>