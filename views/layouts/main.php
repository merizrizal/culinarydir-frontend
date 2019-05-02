<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use frontend\components\AppComponent;
use yii\helpers\Html;

AppAsset::register($this); ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
    <head>
        <meta charset="<?= \Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="app" content="<?= Html::encode(\Yii::$app->name) ?>">
        <?= Html::csrfMetaTags() ?>

        <!-- Favicon -->
        <link rel="icon" href="<?= \Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= \Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= \Yii::$app->request->baseUrl . '/media/favicon.png' ?>">

        <title><?= Html::encode(\Yii::$app->name) . ' - ' . Html::encode($this->title) ?></title>
        <?php $this->head(); ?>
        <?php
        if (\Yii::$app->request->serverName == 'asikmakan.com' || \Yii::$app->request->serverName == 'www.asikmakan.com') {
            echo '
                <!-- Global site tag (gtag.js) - Google Analytics -->
        		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-118083293-1"></script>
        		<script>
        		  window.dataLayer = window.dataLayer || [];
        		  function gtag(){dataLayer.push(arguments);}
        		  gtag("js", new Date());

        		  gtag("config", "UA-118083293-1");
        		</script>
            ';
        } ?>
    </head>
<body>
<?php $this->beginBody() ?>

    <main>

        <?php
        $appComponent = new AppComponent();
        echo $appComponent->navigation();
        echo $appComponent->header(); ?>

        	<?= $content ?>

		<?php
        if (\Yii::$app->request->getUserAgent() != 'com.asikmakan.app'): ?>

            <div class="main">
                <?= $appComponent->appFooter() ?>
            </div>

            <div class="scroll-up"><a href="#totop"><i class="fa fa-angle-double-up fa-2x"></i></a></div>

        <?php
        else: ?>

            <br><br>

        <?php
        endif; ?>

    </main>

    <?php
    if (!empty($this->params['beforeEndBody'])) {
        foreach ($this->params['beforeEndBody'] as $value) {
            $value();
        }
    } ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
