<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\growl\Growl;

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]);

$getFlashMessage = Yii::$app->session->getFlash('message');

if (!empty($getFlashMessage)) {

    echo Growl::widget([
        'type' => $getFlashMessage['type'],
        'title' => $getFlashMessage['title'],
        'icon' => $getFlashMessage['icon'],
        'body' => $getFlashMessage['message'],
        'showSeparator' => true,
        'delay' => $getFlashMessage['delay'],
        'pluginOptions' => [
            'showProgressbar' => false,
            'placement' => [
                'from' => 'bottom',
                'align' => 'left',
            ]
        ]
    ]);
} ?>

<div class="main">
    <section class="module-extra-small bg-main">
        <div class="container register">
            <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4 class="font-alt">Change Password</h4>
                                    <hr class="divider-w mb-20">

                                    <?php $form = ActiveForm::begin([
                                        'id' => 'change-password-form',
                                        'action' => ['user/change-password'],
                                        'fieldConfig' => [
                                            'template' => '{input}{error}',
                                        ]
                                    ]); ?>

                                    <div class="row">
                                        <div class="col-md-12">

                                            <?= $form->field($modelChangePassword, 'current_pass')->passwordInput([
                                                'placeholder' => 'Current Password'
                                            ]) ?>

                                            <?= $form->field($modelChangePassword, 'new_pass')->passwordInput([
                                                'placeholder' => 'New Password'
                                            ]) ?>

                                            <?= $form->field($modelChangePassword, 'confirm_pass')->passwordInput([
                                                'placeholder' => 'Confirm Password'
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row mb-30">
                                        <div class="col-md-12">

                                            <?= Html::submitButton('Save', ['class' => 'btn btn-round btn-d']) ?>
                                            <?= Html::a('Back', Yii::$app->urlManager->createUrl('user'), ['class' => 'btn btn-round btn-default']) ?>

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
