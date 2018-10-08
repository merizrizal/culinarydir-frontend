<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $modelUser core\models\User */

$this->title = $modelUser['full_name'];

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail user-profile">

            <div class="row mb-50">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                
                	<?php
                	$img = '/img/user/default-avatar.png';
                	
                	if (!empty($modelUser['image'])) {
                	    $img = Tools::thumb('/img/user/', $modelUser['image'], 200, 200);
                	}
                	
                	$userNameMail = '
                        <h3>' .
                            $modelUser['full_name'] . '<br>
                            <small>' . $modelUser['email'] . '</small>
                        </h3>
                    ';
                	
                    $btnUpdateProfile = Html::a('<i class="aicon aicon-pencil2"></i> ' . Yii::t('app', 'Update Profile'), ['user/update-profile'], ['class' => 'btn btn-round btn-d']); ?>

                    <div class="row mt-10 visible-lg visible-md visible-sm visible-tab">
                        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-tab-8 col-xs-offset-2">
                            <div class="row ">
                                <div class="widget">
                                    <div class="widget-posts-image">
                                        <?= Html::img(Yii::getAlias('@uploadsUrl') . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']) ?>
                                    </div>
                                    <div class="widget-posts-body">
                                        <?= $userNameMail ?>
                                        <?= $btnUpdateProfile ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row visible-xs">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                	<?= Html::img(Yii::getAlias('@uploadsUrl') . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component center-block']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <?= $userNameMail ?>
                                    <?= $btnUpdateProfile ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="view">
                        <ul class="nav nav-tabs widget mb-10" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#view-journey" aria-controls="view-journey" role="tab" data-toggle="tab">
                                    <ul class="link-icon list-inline">
                                        <li>
                                            <ul class="text-center">
                                                <li><i class="aicon aicon-icon-been-there-fill-1 aicon-1-5x"></i></li>
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
                                                <li><i class="aicon aicon-camera aicon-1-5x"></i><span class="badge total-user-photo"></span></li>
                                                <li><?= Yii::t('app', 'Photo') ?></li>
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
                                                <li><i class="aicon aicon-savedsearch aicon-1-5x"></i><span class="badge total-saved-search">0</span></li>
                                                <li><?= Yii::t('app', 'Saved Search') ?></li>
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
                                                <li><i class="aicon aicon-hot-promo aicon-1-5x"></i><span class="badge total-new-promo"></span></li>
                                                <li><?= Yii::t('app', 'New Promo') ?></li>
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
                                                <li><i class="fa fa-ellipsis-h aicon-1-5x"></i></li>
                                                <li>More <span class="caret"></span></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li role="presentation">
                                        <a href="#view-saved-search" aria-controls="view-saved-search" role="tab" data-toggle="tab">
                                        	<h6><i class="aicon aicon-savedsearch"></i> <?= Yii::t('app', 'Saved Search') ?> (<span class="total-saved-search">0</span>)</h6>
                                    	</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#view-new-promo" aria-controls="view-new-promo" role="tab" data-toggle="tab">
                                        	<h6><i class="aicon aicon-hot-promo"></i> <?= Yii::t('app', 'New Promo') ?> (<span class="total-new-promo"></span>)</h6>
                                    	</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane fade in active p-0" id="view-journey">
                                <?= $this->render('user/_journey', [
                                    'username' => $modelUser['username']
                                ]) ?>
                            </div>

                            <div role="tabpanel" class="tab-pane fade p-0" id="view-photo">
                                <?= $this->render('user/_photo', [
                                    'username' => $modelUser['username']
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
frontend\components\FacebookShare::widget();

$this->params['beforeEndBody'][] = function() {

    Modal::begin([
        'header' => Yii::t('app', 'Confirmation'),
        'id' => 'modal-confirmation',
        'size' => Modal::SIZE_SMALL,
        'footer' => '
            <button class="btn btn-default" data-dismiss="modal" type="button">' . Yii::t('app', 'Cancel') .'</button>
            <button id="btn-delete" class="btn btn-danger" type="button">' . Yii::t('app', 'Delete') .'</button>
        ',
    ]);

        echo Yii::t('app', 'Are you sure want to delete this?');

    Modal::end();
}; ?>