<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput; 

/* @var $this yii\web\View */ 
/* @var $modelBusiness core\models\Business */
/* @var $modelPostPhoto frontend\models\Post */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none;"></div>

            <?php
            $form = ActiveForm::begin([
                'id' => 'post-photo-form',
                'action' => ['action/submit-photo'],
                'fieldConfig' => [
                    'template' => '{input}{error}',
                ]
            ]); ?>

                <?= Html::hiddenInput('business_id', $modelBusiness['id'], ['id' => 'business_id']); ?>
    
                <div class="box-title" id="title-post-photo">
                    <h4 class="mt-0 mb-0 inline-block"><?= Yii::t('app', 'Add Photo') ?></h4>
                    <span class="pull-right inline-block" id="close-post-photo-container"><a class="text-main" href=""><i class="fa fa-close"></i><?= Yii::t('app', 'Cancel') ?></a></span>
                </div>
    
                <div class="box-content">
    
                    <div class="form-group">
                        <button id="post-photo-trigger" type="button" class="btn btn-round btn-d"><i class="fa fa-plus"></i><?= Yii::t('app', 'Add Photo') ?></button>
                    </div>
    
                    <div class="row" id="post-photo-container">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    
                            <?= $form->field($modelPostPhoto, 'text')->textInput([
                                'class' => 'form-control',
                                'placeholder' => Yii::t('app', 'Photo Caption'),
                            ]); ?>
    
                            <?= $form->field($modelPostPhoto, 'image')->widget(FileInput::classname(), [
                                'options' => [
                                    'id' => 'add-photo-input',
                                    'accept' => 'image/*',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'browseClass' => 'btn btn-d',
                                    'showRemove' => false,
                                    'showUpload' => false,
                                    'layoutTemplates' => [
                                        'footer' => '',
                                    ],
                                ]
                            ]); ?>
    
                            <div class="form-group">
    
                                <?= Html::checkboxList('social_media_share', null, [
                                        'facebook' => Yii::t('app', 'Post to Facebook'),
                                    ],
                                    [
                                        'class' => 'social-media-share-list',
                                        'separator' => '&nbsp;&nbsp;&nbsp;',
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                            
                                            return '
                                                <label style="font-weight: normal;">' .
                                                    Html::checkbox($name, $checked, [
                                                        'value' => $value,
                                                        'class' => $value . '-photo-share-trigger icheck',
                                                    ]) . ' ' . $label .
                                                '</label>
                                            ';
                                        },
                                    ]
                                ); ?>
    
                            </div>
    
                            <div class="form-group">
    
                                <?= Html::submitButton('<i class="fa fa-share-square"></i> Upload ' . Yii::t('app', 'Photo'), ['id' => 'submit-post-photo', 'class' => 'btn btn-default btn-standard btn-round']) ?>
    
                                <?= Html::a('<i class="fa fa-times"></i> ' . Yii::t('app', 'Cancel'), null, ['id' => 'cancel-post-photo', 'class' => 'btn btn-default btn-standard btn-round']) ?>
    
                            </div>
                        </div>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<div class="row mt-10">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-content">
                <div class="gallery-section"></div>
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
    function getUserPhoto(business_id) {

        $.ajax({
            cache: false,
            type: "GET",
            data: {
                "business_id": business_id
            },
            url: "' . Yii::$app->urlManager->createUrl(['data/post-photo']) . (!empty($queryParams['redirect']) && $queryParams['redirect'] == 'photo' ? '?page=' . $queryParams['page'] . '&per-page=' . $queryParams['per-page'] : '') . '",
            success: function(response) {

                $(".gallery-section").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPhoto($("#business_id").val());

    $("#post-photo-container").hide();
    $("#close-post-photo-container").hide();

    $("#close-post-photo-container > a, #cancel-post-photo").on("click", function(event) {

        $("#post-photo-container, #close-post-photo-container").fadeOut(100, function() {

            $("#post-photo-trigger").fadeIn();
            $("html, body").animate({ scrollTop: $("#title-post-photo").offset().top }, "slow");
        });

        $("#post-text").val("");
        $("#add-photo-input").fileinput("clear");
        $("#post-photo-container").find(".form-group").removeClass("has-success");
        $("#post-photo-container").find(".form-group").removeClass("has-error");
        $("#post-photo-container").find(".form-group").find(".help-block").html("");

        $(".facebook-photo-share-trigger").iCheck("uncheck");

        return false;
    });

    $("#post-photo-trigger").on("click", function(event) {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl(['redirect/add-photo']) . '",
            success: function(response) {

                thisObj.fadeOut(100, function() {

                    $("#post-photo-container").fadeIn();
                    $("#close-post-photo-container").fadeIn();
                });

                if ($("#post-photo-container").find(".form-group").hasClass("has-error")) {

                    $("#post-photo-container").find(".form-group").removeClass("has-error");
                    $("#post-photo-container").find(".form-group").find(".help-block").html("");
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $("form#post-photo-form").on("beforeSubmit", function(event) {

        var thisObj = $(this);

        thisObj.siblings(".overlay").show();
        thisObj.siblings(".loading-img").show();

        if(thisObj.find(".has-error").length)  {
            return false;
        }

        var formData = new FormData(this);

        var endUrl = thisObj.attr("action");

        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            type: "POST",
            data: formData,
            url: thisObj.attr("action"),
            success: function(response) {

                $("#add-photo-input").fileinput("clear");

                if (response.success) {

                    $("#cancel-post-photo").trigger("click");

                    getUserPhoto($("#business_id").val());

                    $(".facebook-photo-share-trigger").iCheck("uncheck");

                    if ($.trim(response.socialShare)){

                        $.each(response.socialShare, function(socialName, value) {

                            if (socialName === "facebook" && response.socialShare[socialName]) {

                                var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/photo']) . '/" + response.userPostMainPhoto.id;
                                var title = "Foto untuk " + $(".business-name").text().trim();
                                var description = response.userPostMainPhoto.text;
                                var image = window.location.protocol + "//" + window.location.hostname + response.userPostMainPhoto.image;

                                facebookShare({
                                    ogUrl: url,
                                    ogTitle: title,
                                    ogDescription: description,
                                    ogImage: image,
                                    type: "Foto"
                                });
                            }
                        });
                    }

                    messageResponse(response.icon, response.title, response.message, response.type);
                } else {

                    $("#post-photo-container").find(".form-group").removeClass("has-success");
                    $("#post-photo-container").find(".form-group").removeClass("has-error");
                    $("#post-photo-container").find(".form-group").find(".help-block").html("");

                    messageResponse(response.icon, response.title, response.message, response.type);
                }

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                thisObj.siblings(".overlay").hide();
                thisObj.siblings(".loading-img").hide();
            }
        });

        return false;
    });

    $(".gallery-section").on("click", ".share-image-trigger", function() {

        var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/photo']) . '/" + $(this).parents(".work-item").find(".work-image img").data("id");
        var title = "Foto untuk " + $(".business-name").text().trim();
        var description = $(this).parents(".work-item").find(".photo-caption").text() !== "" ? $(this).parents(".work-item").find(".photo-caption").text() : "Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com";
        var image = window.location.protocol + "//" + window.location.hostname + $(this).parents(".work-item").find(".work-image img").attr("src").replace("200x200", "");

        facebookShare({
            ogUrl: url,
            ogTitle: title,
            ogDescription: description,
            ogImage: image,
            type: "Foto"
        });

        return false;
    });
';

$this->registerJs($jscript); ?>