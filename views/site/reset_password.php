<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

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
                                    <h4 class="font-alt">Reset Password</h4>
                                    <hr class="divider-w mb-20">

                                    <h4>
                                        <small>Silakan masukkan password baru Anda.</small>
                                    </h4>

                                    <?php $form = ActiveForm::begin([
                                        'id' => 'reset-password-form',
                                        'options' => [
                                        ],
                                        'fieldConfig' => [
                                            'template' => '{input}{error}',
                                        ]
                                    ]); ?>

                                    <div class="row">
                                        <div class="col-md-12">

                                            <?= $form->field($model, 'password')->passwordInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'New Password',
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12">

                                            <?= Html::submitButton('Save', ['class' => 'btn btn-round btn-d']) ?>

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
frontend\components\GrowlCustom::widget();

$jscript = '';

if (!empty(($message = Yii::$app->session->getFlash('resetSuccess')))) {

    $jscript = 'messageResponse("aicon aicon-icon-tick-in-circle", "Reset Berhasil", "' . $message . '", "success");';
} else if (!empty(($message = Yii::$app->session->getFlash('resetError')))) {

    $jscript = 'messageResponse("aicon aicon-icon-info", "Reset Gagal", "' . $message . '", "danger");';
}

$this->registerJs($jscript); ?>