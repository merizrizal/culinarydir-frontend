<?php

use yii\helpers\Html; ?>

<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#custom-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= Yii::$app->urlManager->createUrl(['']); ?>"><img class="img-responsive img-component" src="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-logo.png' ?>" style="height: 30px; margin-top: 5px;"></a>
        </div>
        <div class="collapse navbar-collapse" id="custom-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>

                    <?= Html::a('<i class="aicon aicon-home4 aicon-2x"></i>', Yii::$app->urlManager->createUrl(['']), ['class' => 'icon hidden-xs']) ?>
                    <?= Html::a('<i class="aicon aicon-home4 aicon-2x"></i> Home', Yii::$app->urlManager->createUrl(['']), ['class' => 'icon visible-tab']) ?>
                    <?= Html::a('<i class="aicon aicon-home4 aicon-2x"></i> Home', Yii::$app->urlManager->createUrl(['']), ['class' => 'icon visible-xs']) ?>

                </li>

                <?php
                if (!Yii::$app->user->isGuest): ?>

                <!--<li><a class="icon" href="#"><i class="aicon aicon-heart2 aicon-2x"></i></a></li>
                <li><a class="icon" href="#"><i class="aicon aicon-savedsearch aicon-2x"></i></a></li>-->
                <li class="dropdown">

                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i>', '#', ['class' => 'icon dropdown-toggle hidden-xs', 'data-toggle' => 'dropdown']) ?>
                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-tab', 'data-toggle' => 'dropdown']) ?>
                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-xs', 'data-toggle' => 'dropdown']) ?>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="icon" href="<?= Yii::$app->urlManager->createUrl(['user']) ?>">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="widget">
                                            <div class="widget-posts-image">

                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty(Yii::$app->user->getIdentity()->image) ? Yii::$app->user->getIdentity()->thumb('/img/user/', 'image', 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-profile-thumb img-component']) ?>

                                            </div>
                                            <div class="widget-posts-body">
                                                <strong><?= Yii::$app->user->getIdentity()->full_name ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                                                <br>
                                                <small><?= Yii::$app->user->getIdentity()->email ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><?= Html::a('<i class="aicon aicon-key2"></i> Change Password', Yii::$app->urlManager->createUrl(['user/change-password'])) ?></li>
                        <li><?= Html::a('<i class="aicon aicon-logout"></i> Sign Out', Yii::$app->urlManager->createUrl(['site/logout']), ['data-method' => 'post']) ?></li>
                    </ul>
                </li>

                <?php
                else: ?>

                <!--<li><a class="icon" href="#"><i class="aicon aicon-heart2 aicon-2x"></i></a></li>
                <li><a class="icon" href="#"><i class="aicon aicon-savedsearch aicon-2x"></i></a></li>-->
                <li class="dropdown">

                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i>', '#', ['class' => 'icon dropdown-toggle hidden-xs', 'data-toggle' => 'dropdown']) ?>
                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-tab', 'data-toggle' => 'dropdown']) ?>
                    <?= Html::a('<i class="aicon aicon-user2 aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-xs', 'data-toggle' => 'dropdown']) ?>

                    <ul class="dropdown-menu">
                        <li><?= Html::a('Daftar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', Yii::$app->urlManager->createUrl(['site/register'])) ?></li>
                        <li><?= Html::a('Masuk', Yii::$app->urlManager->createUrl(['site/login'])) ?></li>
                    </ul>
                </li>

                <?php
                endif; ?>

            </ul>
        </div>
    </div>
</nav>