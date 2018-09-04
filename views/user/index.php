<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
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
                <div class="col-sm-12 col-xs-12">
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
                                        <h3>
                                            <?= $modelUser['full_name'] ?><br>
                                            <small><?= $modelUser['email'] ?></small>
                                        </h3>

                                        <?= Html::a('<i class="aicon aicon-pencil2"></i> Update Profile', ['user/update-profile'], ['class' => 'btn btn-round btn-d']) ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row visible-xs">
                        <div class="col-xs-12">
                            <div class="row ">
                                <div class="col-xs-12">

                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($modelUser['image']) ? Tools::thumb('/img/user/', $modelUser['image'], 200, 200) : '/img/user/default-avatar.png'), [
                                        'class' => 'img-responsive img-circle img-profile-thumb img-component center-block'
                                    ]) ?>

                                </div>
                                <div class="col-xs-12 text-center">
                                    <h3>
                                        <?= $modelUser['full_name'] ?><br>
                                        <small><?= $modelUser['email'] ?></small>
                                    </h3>

                                    <?= Html::a('<i class="aicon aicon-pencil2"></i> Update Profile', ['user/update-profile'], ['class' => 'btn btn-round btn-d']) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-sm-12 col-xs-12">

                    <div class="view">
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
                                <a href="#view-photo" aria-controls="view-photo" role="tab" data-toggle="tab" class="nav-tabs-photo">
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
                            <li role="presentation" class="visible-lg visible-md visible-sm visible-tab">
                                <a href="#view-saved-search" aria-controls="view-saved-search" role="tab" data-toggle="tab">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <i class="aicon aicon-savedsearch aicon-1-5x"></i>
                                                <li>Saved Search</li>
                                                <span class="badge total-saved-search">0</span>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                            </li>
                            <li role="presentation" class="visible-lg visible-md visible-sm visible-tab">
                                <a href="#view-new-promo" aria-controls="view-new-promo" role="tab" data-toggle="tab">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <i class="aicon aicon-hot-promo aicon-1-5x"></i>
                                                <li>New Promo</li>
                                                <span class="badge total-new-promo"></span>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                            </li>
                            <li role="presentation" class="dropdown visible-xs">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <i class="fa fa-ellipsis-h aicon-1-5x"></i>
                                                <li>More <span class="caret"></span></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li role="presentation">
                                        <a href="#view-saved-search" aria-controls="view-saved-search" role="tab" data-toggle="tab"><h6><i class="aicon aicon-savedsearch"></i> Saved Search (<span class="total-saved-search">0</span>)</h6></a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#view-new-promo" aria-controls="view-new-promo" role="tab" data-toggle="tab"><h6><i class="aicon aicon-hot-promo"></i> New Promo (<span class="total-new-promo"></span>)</h6></a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane fade in active p-0" id="view-journey">
                                <?= $this->render('user/_journey') ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-photo">
                                <?= $this->render('user/_photo', [
                                    'username' => null
                                ]) ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-saved-search">
                                <?= $this->render('user/_saved_search') ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-new-promo">
                                <?= $this->render('user/_new_promo') ?>
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

frontend\components\GrowlCustom::widget();

$this->params['beforeEndBody'][] = function() {

    Modal::begin([
        'header' => 'Konfirmasi',
        'id' => 'modal-confirmation',
        'footer' => '<button class="btn btn-default" data-dismiss="modal" type="button">Batal</button>
                    <button id="btn-delete" class="btn btn-danger" type="button">Hapus</button>',
    ]);

    echo 'Anda yakin akan menghapus foto ini?';

    Modal::end();
}; ?>