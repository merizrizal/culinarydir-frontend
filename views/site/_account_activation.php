<?php

use yii\helpers\Html;

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
                                    <h4 class="font-alt"><?= Yii::t('app', 'Your Account Has Been Activated') ?></h4>
                                    <hr class="divider-w mb-20">

                                    <h5>Halo, <?= $modelUser['full_name'] ?> !</h5>

                                    <div class="mb-10"><small>Silakan masuk dengan Email / Username Anda dengan mengklik link di bawah.</small></div>

                                    <?= Html::a(Yii::t('app', 'Login To') . Yii::$app->name, ['site/login']) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>