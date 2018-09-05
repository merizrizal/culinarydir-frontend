<?php

use yii\helpers\Html;
use frontend\components\AppComponent;
use sycomponent\Tools;

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]);

$appComponent = new AppComponent(); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">

            <div class="row mb-50">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row mt-10 visible-lg visible-md visible-sm visible-tab">
                        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-tab-8 col-xs-offset-2">
                            <div class="row ">
                                <div class="widget">
                                    <div class="widget-posts-image">

                                        <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($modelUser['image']) ? Tools::thumb('/img/user/', $modelUser['image'], 200, 200) : '/img/user/default-avatar.png'), [
                                            'class' => 'img-responsive img-circle img-profile-thumb img-component'
                                        ]) ?>

                                    </div>
                                    <div class="widget-posts-body">
                                        <h3 class="mt-40">
                                            <?= $modelUser['full_name'] ?><br>
                                            <small><?= $modelUser['email'] ?></small>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row visible-xs">
                        <div class="col-xs-12">
                            <div class="row ">
                                <div class="col-xs-12 text-center">

                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($modelUser['image']) ? Tools::thumb('/img/user/', $modelUser['image'], 200, 200) : '/img/user/default-avatar.png'), [
                                        'class' => 'img-responsive img-circle img-profile-thumb img-component center-block'
                                    ]) ?>

                                </div>
                                <div class="col-xs-12 text-center">
                                    <h3>
                                        <?= $modelUser['full_name'] ?><br>
                                        <small><?= $modelUser['email'] ?></small>
                                    </h3>
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
                                                <i class="aicon aicon-camera aicon-1-5x"></i>
                                                <li>Photo</li>
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
                                    'username' => Yii::$app->request->get('user')
                                ]) ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-photo">
                                <?= $this->render('user/_photo', [
                                    'username' => Yii::$app->request->get('user')
                                ]) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>
<div class="scroll-up"><a href="#totop"><i class="fa fa-angle-double-up fa-2x"></i></a></div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']); ?>