<?php

use core\models\City;
use frontend\components\GrowlCustom;
use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $modelUserPerson core\models\UserPerson */
/* @var $modelUser core\models\User */
/* @var $modelPerson core\models\Person */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$this->title = 'Update Profile ' . $modelUser->full_name;

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail user-update-profile">

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="box bg-white">
                        <div class="box-content">
                            <div class="row mt-10">
                                <div class="col-md-12 text-center">
                                    <h4 class="font-alt"><?= \Yii::t('app', 'Update Profile') ?></h4>
                                </div>
                                <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                                    <div class="widget">

                                        <?php
                                        $form = ActiveForm::begin([
                                            'id' => 'update-profile-form',
                                            'action' => ['user/update-profile'],
                                            'options' => ['enctype' => 'multipart/form-data'],
                                            'fieldConfig' => [
                                                'template' => '{input}{error}',
                                            ]
                                        ]); ?>

                                            <div class="profile-img-container img-circle mb-20">

                                            	<?php
                                            	$img = !empty(\Yii::$app->user->getIdentity()->image) ? \Yii::$app->user->getIdentity()->image . '&w=200&h=200' : 'default-avatar.png';

                                                echo $form->field($modelUser, 'image')->widget(FileInput::classname(), [
                                                    'options' => [
                                                        'id' => 'input-img-profile',
                                                        'accept' => 'image/jpeg',
                                                    ],
                                                    'pluginOptions' => [
                                                        'initialPreview' => [
                                                            Html::img(\Yii::$app->params['endPointLoadImage'] . 'user?image=' . $img, ['class' => 'img-responsive img-component img-profile']),
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

                                                <?= $form->field($modelPerson, 'first_name')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => \Yii::t('app', 'First Name'),
                                                ]) ?>


                                                <?= $form->field($modelPerson, 'last_name')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => \Yii::t('app', 'Last Name'),
                                                ]) ?>

                                                <?= $form->field($modelPerson, 'about_me')->textarea([
                                                    'rows' => 3,
                                                    'placeholder' => \Yii::t('app', 'About Me')
                                                ]) ?>

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

                                                <?= $form->field($modelPerson, 'address')->textarea([
                                                    'rows' => 3,
                                                    'placeholder' => \Yii::t('app', 'Address')
                                                ]) ?>

                                                <?= $form->field($modelPerson, 'phone')->widget(MaskedInput::className(), [
                                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                    'options' => [
                                                        'class' => 'form-control',
                                                        'placeholder' => \Yii::t('app', 'Phone'),
                                                    ],
                                                ]) ?>

                                                <?= $form->field($modelUser, 'email', [
                                                    'enableAjaxValidation' => true
                                                ])->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Email',
                                                ]) ?>

                                                <?= $form->field($modelUser, 'username', [
                                                    'enableAjaxValidation' => true
                                                ])->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Username',
                                                    'readonly' => 'readonly',
                                                ]) ?>

                                                <?= Html::submitButton('Update', ['class' => 'btn btn-round btn-d mb-30']) ?>
                                                <?= Html::a(\Yii::t('app', 'Cancel'), ['user/index'], ['class' => 'btn btn-round btn-default mb-30']) ?>

                                            </div>

                                        <?php
                                        ActiveForm::end(); ?>

                                    </div>
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
$cssScript = '
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

$this->registerCss($cssScript);

GrowlCustom::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $("#person-city_id").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'City') . '",
        minimumResultsForSearch: -1
    });
';

if (!empty(($message = \Yii::$app->session->getFlash('message')))) {

    $jscript .= 'messageResponse("aicon aicon-icon-tick-in-circle", "' . \Yii::t('app', 'Update Profile Successful') . '", "' . $message['message'] . '", "success");';
}

$this->registerJs($jscript); ?>