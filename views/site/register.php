<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use core\models\City;
use kartik\growl\Growl;
use yii\authclient\widgets\AuthChoice;

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
kartik\select2\ThemeKrajeeAsset::register($this);

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
    <section class="module-small bg-main">
        <div class="container register">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="font-alt">Daftar</h4>
                                    <hr class="divider-w mb-20">

                                    <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'register-form',
                                        'action' => 'register',
                                        'options' => [
                                        ],
                                        'fieldConfig' => [
                                            'template' => '{input}{error}',
                                        ]
                                    ]);

                                    if (!empty($socmed)) {

                                        if ($socmed === 'Facebook') {

                                            echo $form->field($modelUserSocialMedia, 'facebook_id')->hiddenInput(['class' => 'form-control']);

                                        } else if ($socmed === 'Google') {

                                            echo $form->field($modelUserSocialMedia, 'google_id')->hiddenInput(['class' => 'form-control']);

                                        }

                                    } ?>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelPerson, 'first_name')->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('app', 'First Name'),
                                            ]) ?>

                                        </div>
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelPerson, 'last_name')->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('app', 'Last Name'),
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelPerson, 'phone')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'class' => 'form-control',
                                                    'placeholder' => Yii::t('app', 'Phone'),
                                                ],
                                            ]) ?>

                                        </div>
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelPerson, 'city_id')->dropDownList(
                                                ArrayHelper::map(
                                                    City::find()->orderBy('name')->asArray()->all(),
                                                    'id',
                                                    function($data) {
                                                        return $data['name'];
                                                    }
                                                ),
                                                [
                                                    'prompt' => '',
                                                    'style' => 'width: 100%'
                                                ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelUserRegister, 'username', [
                                                'enableAjaxValidation' => true
                                            ])->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Username',
                                            ]) ?>

                                        </div>
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelUserRegister, 'email', [
                                                'enableAjaxValidation' => true
                                            ])->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Email',
                                                'readonly' => !empty($socmed),
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelUserRegister, 'password')->passwordInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Password',
                                            ]) ?>

                                        </div>
                                        <div class="col-md-6 col-sm-6">

                                            <?= $form->field($modelUserRegister, 'password_repeat')->passwordInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Repeat Password',
                                            ]) ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12">

                                            <?= Html::submitButton('Daftar', ['class' => 'btn btn-block btn-round btn-d']) ?>

                                            <div class="mt-20 mb-20 align-center"><?= Yii::t('app', 'OR') ?></div>

                                            <div class="mt-10">

                                                <?php
                                                $authAuthChoice = AuthChoice::begin([
                                                    'baseAuthUrl' => ['site/auth'],
                                                    'popupMode' => false,
                                                ]);

                                                foreach ($authAuthChoice->getClients() as $client):

                                                    $btnType = '';

                                                    if ($client->getName() === 'facebook') {
                                                        $btnType = 'btn-primary';
                                                    } else if ($client->getName() === 'google') {
                                                        $btnType = 'btn-border-d';
                                                    }

                                                    echo $authAuthChoice->clientLink($client,
                                                        '<i class="fab fa-' . $client->getName() . '"></i> ' . Yii::t('app', 'Sign In With') . $client->getTitle(), [
                                                        'class' => 'btn ' . $btnType . ' btn-block btn-round',
                                                    ]);

                                                endforeach;

                                                AuthChoice::end(); ?>

                                            </div>

                                            <hr class="divider-w mt-20 mb-10">

                                            <div class="text-center">
                                                <h4>
                                                    <small>Sudah memiliki Akun? <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>">Masuk</a></small>
                                                </h4>
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
$jscript = '
    $("#person-city_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City') . '",
        minimumResultsForSearch: -1
    });
';

$this->registerJs($jscript); ?>