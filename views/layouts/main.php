<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use frontend\components\AppComponent;

AppAsset::register($this); ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="app" content="<?= Html::encode(Yii::$app->name) ?>">
        <?= Html::csrfMetaTags() ?>

        <!-- Favicon -->
        <link rel="icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>">

        <title><?= Html::encode(Yii::$app->name) ?></title>
        <?php $this->head(); ?>
    </head>
<body>
<?php $this->beginBody() ?>

    <main>
        <div class="page-loader">
            <div class="loader">Loading...</div>
        </div>

        <?php
        $appComponent = new AppComponent();
        echo $appComponent->navigation();
        echo $appComponent->header(); ?>

            <?= $content ?>

        <div class="main">
            <?= $appComponent->appFooter() ?>
        </div>
        <div class="scroll-up"><a href="#totop"><i class="fa fa-angle-double-up fa-2x"></i></a></div>
    </main>

    <?php
    if (!empty($this->params['beforeEndBody'])) {
        foreach ($this->params['beforeEndBody'] as $value) {
            $value();
        }
    }

    $jscript = '
        window.fbAsyncInit = function() {
            FB.init({
                appId            : "' . Yii::$app->params['facebook']['clientId'] . '",
                autoLogAppEvents : true,
                xfbml            : true,
                version          : "v3.1"
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, "script", "facebook-jssdk"));
    ';

    $this->registerJs($jscript); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
