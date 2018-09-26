<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name; ?>

<section class="home-section home-parallax home-fade home-full-height bg-dark bg-dark-60" id="home" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
    <div class="titan-caption">
        <div class="caption-content">
            <div class="font-alt mb-30 titan-title-size-4"><?= Html::encode($this->title) ?></div>
            <div class="font-alt"><?= nl2br(Html::encode($message)) ?>
            </div>
            <div class="font-alt mt-30">
                <?= Html::a(Yii::t('app', 'Back To Home Page'), ['page/index'], ['class' => 'btn btn-border-w btn-round']) ?>
            </div>
        </div>
    </div>
</section>
