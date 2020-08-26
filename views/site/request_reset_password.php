<?php

use frontend\components\GrowlCustom;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RequestResetPassword */
/* @var $verification bool */

$this->title = 'Reset Password';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Kuliner Bandung Club.com'
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

                                    <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'request-reset-password-form',
                                        'action' => ['site/request-reset-password', 'verification' => $verification, 'email' => $model->email],
                                        'options' => [
                                        ],
                                        'fieldConfig' => [
                                            'template' => '{input}{error}',
                                        ]
                                    ]);
                                        if (!$verification): ?>

                                            <?= \Yii::t('app', 'Please enter your email.') ?>

                                            <div class="row">
                                                <div class="col-md-12">

                                                    <?= $form->field($model, 'email', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput([
                                                        'class' => 'form-control',
                                                        'placeholder' => $model->getAttributeLabel('email'),
                                                    ]) ?>

                                                </div>
                                            </div>

                                        <?php
                                        else: ?>

                                        	<?= \Yii::t('app', 'We have sent a verification code to') . ' ' . $model->email . '.<br>' .  \Yii::t('app', 'Please check.') ?>

                                            <div class="row">
                                                <div class="col-md-12">

                                                	<?= $form->field($model, 'verificationCode', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput([
                                                        'class' => 'form-control',
                                                	    'placeholder' => $model->getAttributeLabel('verificationCode'),
                                                    ]) ?>

                                                </div>
                                            </div>

                                        <?php
                                        endif; ?>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                <?= Html::submitButton(\Yii::t('app', !$verification ? 'Next' : 'Send Request'), ['class' => 'btn btn-round btn-d']) ?>

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