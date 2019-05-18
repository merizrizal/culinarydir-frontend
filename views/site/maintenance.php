<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = 'Maintenance'; ?>

<section class="home-section home-parallax home-fade home-full-height bg-dark bg-dark-60" id="home" data-background="<?= \Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
    <div class="titan-caption">
        <div class="caption-content">
            <div class="font-alt mb-30 titan-title-size-4"><?= Html::encode($this->title) ?></div>
            <div class="font-alt"><?= nl2br(\Yii::t('app', 'Website Currently Under Maintenance')) ?></div>
        </div>
    </div>
</section>
