<?php

use frontend\components\GrowlCustom;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model frontend\models\ResetPassword */

$this->title = 'Reset Password';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
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

                                    <?= \Yii::t('app', 'Please enter your new password.') ?>

                                    <?php
                                    $form = ActiveForm::begin([
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
                                                    'placeholder' => \Yii::t('app', 'New Password'),
                                                ]) ?>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-round btn-d']) ?>

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
GrowlCustom::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '';

if (!empty(($message = \Yii::$app->session->getFlash('resetSuccess')))) {

    $jscript = 'messageResponse("aicon aicon-icon-tick-in-circle", "Reset Berhasil", "' . $message . '", "success");';
} else if (!empty(($message = \Yii::$app->session->getFlash('resetError')))) {

    $jscript = 'messageResponse("aicon aicon-icon-info", "Reset Gagal", "' . $message . '", "danger");';
}

$this->registerJs($jscript); ?>