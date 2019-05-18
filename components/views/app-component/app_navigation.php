<?php

use yii\helpers\Html;
?>

<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#custom-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <?php
            $img = Html::img(\Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-logo.png', ['class' => 'img-responsive img-component']);

            echo Html::a($img, ['page/index'], ['class' => 'navbar-brand']) ?>

        </div>
        <div class="collapse navbar-collapse" id="custom-collapse">
            <ul class="nav navbar-nav navbar-right">
            	<li>

                    <?= Html::a('<i class="aicon aicon-home aicon-2x"></i>', ['page/index'], ['class' => 'icon hidden-xs']) ?>
                    <?= Html::a('<i class="aicon aicon-home aicon-2x"></i> Home', ['page/index'], ['class' => 'icon visible-tab']) ?>
                    <?= Html::a('<i class="aicon aicon-home aicon-2x"></i> Home', ['page/index'], ['class' => 'icon visible-xs']) ?>

                </li>

                <li>

            		<?= Html::a('<i class="aicon aicon-icon-online-ordering aicon-2x"></i>', ['order/checkout'], ['class' => 'icon hidden-xs']) ?>
            		<?= Html::a('<i class="aicon aicon-icon-online-ordering aicon-2x"></i> ' . \Yii::t('app', 'Order List'), ['order/checkout'], ['class' => 'icon visible-tab']) ?>
                    <?= Html::a('<i class="aicon aicon-icon-online-ordering aicon-2x"></i> ' . \Yii::t('app', 'Order List'), ['order/checkout'], ['class' => 'icon visible-xs']) ?>

            	</li>

                <?php
                if (!\Yii::$app->user->isGuest): ?>

                    <!--<li><a class="icon" href="#"><i class="aicon aicon-heart2 aicon-2x"></i></a></li>
                    <li><a class="icon" href="#"><i class="aicon aicon-savedsearch aicon-2x"></i></a></li>-->
                    <li class="dropdown">

                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i>', '#', ['class' => 'icon dropdown-toggle hidden-xs', 'data-toggle' => 'dropdown']) ?>
                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-tab', 'data-toggle' => 'dropdown']) ?>
                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-xs', 'data-toggle' => 'dropdown']) ?>

                        <ul class="dropdown-menu dropdown-user">
                            <li>
                                <a class="icon" href="<?= \Yii::$app->urlManager->createUrl(['user']) ?>">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="widget">
                                                <div class="widget-posts-image">

                                                	<?php
                                                	$img = \Yii::$app->params['endPointLoadImage'] . 'user?image=default-avatar.png';

                                                	if (!empty(\Yii::$app->user->getIdentity()->image)) {

                                                	    $img = \Yii::$app->params['endPointLoadImage'] . 'user?image=' . \Yii::$app->user->getIdentity()->image . '&w=32&h=32';
                                                	}

                                                	echo Html::img($img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']) ?>

                                                </div>
                                                <div class="widget-posts-body">
                                                    <strong><?= \Yii::$app->user->getIdentity()->full_name ?>&nbsp;&nbsp;&nbsp;</strong>
                                                    <br>
                                                    <small><?= \Yii::$app->user->getIdentity()->email ?>&nbsp;&nbsp;&nbsp;</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li><?= Html::a('<i class="aicon aicon-key1"></i> ' . \Yii::t('app', 'Change Password'), ['user/change-password']) ?></li>
                            <li><?= Html::a('<i class="aicon aicon-logout"></i> ' . \Yii::t('app', 'Logout'), ['site/logout']) ?></li>
                        </ul>
                    </li>

                <?php
                else: ?>

                    <li class="dropdown">

                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i>', '#', ['class' => 'icon dropdown-toggle hidden-xs', 'data-toggle' => 'dropdown']) ?>
                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-tab', 'data-toggle' => 'dropdown']) ?>
                        <?= Html::a('<i class="aicon aicon-user aicon-2x"></i> User', '#', ['class' => 'icon dropdown-toggle visible-xs', 'data-toggle' => 'dropdown']) ?>

                        <ul class="dropdown-menu">
                            <li><?= Html::a(\Yii::t('app', 'Register'), ['site/register']) ?></li>
                            <li><?= Html::a(\Yii::t('app', 'Login'), ['site/login']) ?></li>
                        </ul>
                    </li>

                <?php
                endif; ?>

            </ul>
        </div>
    </div>
</nav>