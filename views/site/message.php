<?php
use yii\helpers\Html;

/* @var $this yii\web\view */
/* @var $title frontend\controllers\SiteController */
/* @var $fullname frontend\controllers\SiteController */
/* @var $messages frontend\controllers\SiteController */
/* @var $links frontend\controllers\SiteController */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]); ?>

<div class="main">
    <section class="module-small bg-main">
        <div class="container register">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="font-alt"><?= $title ?></h4>
                                    <hr class="divider-w mb-20">

                                    <h5><?= Yii::t('app', 'Hello') . ', ' . $fullname ?> !</h5>

                                    <div class="mb-10"><small><?= $messages ?></small></div>

                                    <?= !empty($links) ? Html::a($links['name'], $links['url']) : '' ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>