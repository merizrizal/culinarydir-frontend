<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]);

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this); ?>

<div class="main">
    <section class="module-small bg-main">
        <div class="container register">
            <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="font-alt">Masuk</h4>
                                    <hr class="divider-w mb-20">

                                    <?php $form = ActiveForm::begin([
                                        'id' => 'login-form',
                                        'action' => 'login',
                                        'options' => [
                                        ],
                                        'fieldConfig' => [
                                            'template' => '{input}{error}',
                                        ]
                                    ]); ?>

                                    <div class="row">
                                        <div class="col-md-12">

                                            <?= $form->field($model, 'login_id')->textInput([
                                                'id' => 'login_id',
                                                'class' => 'form-control',
                                                'placeholder' => $model->getAttributeLabel('login_id')
                                            ]) ?>

                                        </div>
                                        <div class="col-md-12">

                                            <?= $form->field($model, 'password')->passwordInput([
                                                'id' => 'password',
                                                'class' => 'form-control',
                                                'placeholder' => $model->getAttributeLabel('password')
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-7">

                                                    <?= $form->field($model, 'rememberMe')->checkbox() ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <?= Html::submitButton('Masuk', ['class' => 'btn btn-block btn-round btn-d', 'name' => 'loginButton', 'value' => 'loginButton']) ?>

                                                <hr class="divider-w mt-20 mb-10">

                                                <div class="text-center">
                                                    <h4>
                                                        <small>Belum memiliki Akun? <a href="<?= Yii::$app->urlManager->createUrl(['site/register']) ?>">Daftar</a></small>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php ActiveForm::end(); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(Yii::$app->params['checkbox-radio-script']()); ?>

