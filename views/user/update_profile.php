<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use backend\models\City;
use kartik\file\FileInput;
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

    <section class="module-extra-small bg-main">
        <div class="container detail user-update-profile">

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row mt-10">
                                <div class="col-md-12 text-center">
                                    <h2>Update Profile</h2>
                                </div>
                                <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                                    <div class="widget">

                                        <?php $form = ActiveForm::begin([
                                            'id' => 'update-profile-form',
                                            'action' => ['user/update-profile'],
                                            'options' => ['enctype' => 'multipart/form-data'],
                                            'fieldConfig' => [
                                                'template' => '{input}{error}',
                                            ]
                                        ]); ?>

                                        <div class="profile-img-container img-circle mb-20">

                                            <?= $form->field($modelUserPerson->user, 'image')->widget(FileInput::classname(), [
                                                'options' => [
                                                    'id' => 'input-img-profile',
                                                    'accept' => 'image/jpeg',
                                                ],
                                                'pluginOptions' => [
                                                    'initialPreview' => [
                                                        Html::img(Yii::getAlias('@uploadsUrl') . (!empty(Yii::$app->user->getIdentity()->image) ? Yii::$app->user->getIdentity()->thumb('/img/user/', 'image', 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-component img-profile']),
                                                    ],
                                                    'showRemove' => false,
                                                    'showUpload' => false,
                                                    'showCaption' => false,
                                                    'browseIcon' => '<i class="fa fa-upload"></i>',
                                                    'browseLabel' =>  '',
                                                    'browseClass' =>  'btn btn-d',
                                                    'layoutTemplates' => [
                                                        'preview' => '<div class="file-preview-thumbnails"></div>',
                                                    ],
                                                    'previewSettings' => [
                                                        'image' => [
                                                            'position' => 'relative',
                                                            'top' => '35px',
                                                            'width' => 'auto',
                                                            'height' => '160px',
                                                        ]
                                                    ],
                                                    'previewTemplates' => [
                                                        'generic' => '
                                                            <div class="file-preview-frame file-preview-initial file-sortable kv-preview-thumb" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
                                                                <div class="kv-file-content">{content}</div>
                                                            </div>',
                                                        'image' => '
                                                            <div class="file-preview-frame file-preview-initial file-sortable kv-preview-thumb" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">
                                                                <div class="kv-file-content">
                                                                    <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}" {style}>
                                                                </div>
                                                            </div>'
                                                    ]
                                                ]
                                            ]); ?>

                                        </div>

                                        <div class="widget-posts-body">

                                            <?= $form->field($modelUserPerson->person, 'first_name')->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('app', 'First Name'),
                                            ]) ?>


                                            <?= $form->field($modelUserPerson->person, 'last_name')->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('app', 'Last Name'),
                                            ]) ?>

                                            <?= $form->field($modelUserPerson->person, 'about_me')->textarea([
                                                'rows' => 3,
                                                'placeholder' => Yii::t('app', 'About Me')
                                            ]) ?>

                                            <?= $form->field($modelUserPerson->person, 'city_id')->dropDownList(
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

                                            <?= $form->field($modelUserPerson->person, 'address')->textarea([
                                                'rows' => 3,
                                                'placeholder' => Yii::t('app', 'Address')
                                            ]) ?>

                                            <?= $form->field($modelUserPerson->person, 'phone')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'class' => 'form-control',
                                                    'placeholder' => Yii::t('app', 'Phone'),
                                                ],
                                            ]) ?>

                                            <?= $form->field($modelUserPerson->user, 'email', [
                                                'enableAjaxValidation' => true
                                            ])->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Email',
                                            ]) ?>

                                            <?= $form->field($modelUserPerson->user, 'username', [
                                                'enableAjaxValidation' => true
                                            ])->textInput([
                                                'class' => 'form-control',
                                                'placeholder' => 'Username',
                                                'readonly' => 'readonly',
                                            ]) ?>

                                            <?= Html::submitButton('Update', ['class' => 'btn btn-round btn-d mb-30']) ?>
                                            <?= Html::a('Back', Yii::$app->urlManager->createUrl('user'), ['class' => 'btn btn-round btn-default mb-30']) ?>

                                        </div>

                                        <?php ActiveForm::end(); ?>

                                    </div>
                                </div>
                                <div class="col-xs-12 visible-xs">

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
$csscript = '
    .img-profile {
        position: relative;
        top: 35px;
    }

    .file-input {
        position: absolute;
        bottom:0;
        left:0;
        width:100%;
    }

    .btn-file {
        width:100%;
    }
';

$this->registerCss($csscript);

$jscript = '
    $("#person-city_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City') . '",
        minimumResultsForSearch: -1
    });
';

$this->registerJs($jscript); ?>
