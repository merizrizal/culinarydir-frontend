<?php

use yii\helpers\Html;
use sycomponent\Tools; ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail photo">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> Back to Place Detail', Yii::$app->urlManager->createUrl(['page/detail', 'id' => $modelUserPostMain['business']['id']])) ?>

                </div>
            </div>

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row mt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="box bg-white">
                                <div class="box-content">

                                    <?php
                                    if (!empty($modelUserPostMain)): ?>

                                        <div class="photo-container">
    
                                            <?= Html::hiddenInput('business_name', $modelUserPostMain['business']['name'], ['class' => 'business-name']) ?>
    
                                            <?= Html::hiddenInput('user_post_main_id', $modelUserPostMain['id'], ['class' => 'user-post-main-id']) ?>
    
                                            <div class="row mb-10">
                                                <div class="col-md-4 col-sm-5 col-xs-6 visible-lg visible-md visible-sm visible-tab">
                                                    <div class="widget">
                                                        <div class="widget-posts-image">
    
                                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($modelUserPostMain['user']['image']) ? Tools::thumb('/img/user/', $modelUserPostMain['user']['image'], 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-profile-thumb img-component']); ?>
    
                                                        </div>
    
                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $modelUserPostMain['user']['username']])); ?>
                                                            <br>
                                                            <small><?= Yii::$app->formatter->asRelativeTime($modelUserPostMain['created_at']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-9 visible-xs">
                                                    <div class="widget">
                                                        <div class="widget-posts-image">
    
                                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($modelUserPostMain['user']['image']) ? Tools::thumb('/img/user/', $modelUserPostMain['user']['image'], 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-profile-thumb img-component']); ?>
    
                                                        </div>
    
                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $modelUserPostMain['user']['username']])); ?>
                                                            <br>
                                                            <small><?= Yii::$app->formatter->asRelativeTime($modelUserPostMain['created_at']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="photo-review mt-10 mb-10">
                                                        <div class="row">
                                                            <div class="col-sm-12 text-center">
    
                                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $modelUserPostMain['image']) ?>
    
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <p class="review-description">
    
                                                        <?= $modelUserPostMain['text']; ?>
    
                                                    </p>
    												
    												<?php                   
                                					$loveCount = !empty($modelUserPostMain['love_value']) ? $modelUserPostMain['love_value'] : 0;
                                					$commentCount = !empty($modelUserPostMain['userPostComments']) ? count($modelUserPostMain['userPostComments']) : 0; ?>
    												
                                                    <div class="row visible-xs">
                                                        <div class="col-xs-3">
                                                            <ul class="list-inline mt-0 mb-0">
                                                                <li>
    
                                                                    <small><?= '<i class="fa fa-thumbs-up"></i> <span class="total-' . $modelUserPostMain['id'] . '-likes-review">' . $loveCount . '</span>' ?></small>
    
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-xs-9 text-right">
                                                            <ul class="list-inline mt-0 mb-0">
                                                                <li>
    
                                                                    <?php 
                                                                	$spanCount = '<span class="total-' . $modelUserPostMain['id'] . '-comments-review">#</span>'; ?>
                                
                                                                    <small><?= Yii::t('app', '{value, plural, =0{' . $spanCount .' Comment} =1{' . $spanCount .' Comment} other{' . $spanCount .' Comments}}', ['value' => $commentCount]) ?></small>
    
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
    
                                                    <div class="row">
                                                        <div class="col-sm-7 col-tab-7 col-xs-12">
                                                            <ul class="list-inline list-review mt-0 mb-0">
                                                                <li>
    
                                                                    <?php
                                                                    $selected = !empty($modelUserPostMain['userPostLoves'][0]) ? 'selected' : '';
    						
                            										$spanCount = '<span class="total-' . $modelUserPostMain['id'] . '-likes-review">#</span>'; ?>
                                
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> ' . Yii::t('app', '{value, plural, =0{' . $spanCount .' Like} =1{' . $spanCount .' Like} other{' . $spanCount .' Likes}}', ['value' => $loveCount]), null , ['class' => 'user-' . $modelUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', null, ['class' => 'user-' . $modelUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-xs']); ?>
    
                                                                </li>
                                                                <li>
    
                                                                    <?php 
                                                                	$spanCount = '<span class="total-' . $modelUserPostMain['id'] . '-comments-review">#</span>'; ?>
                                
                                                                    <?= Html::a('<i class="fa fa-comments"></i> ' . Yii::t('app', '{value, plural, =0{' . $spanCount .' Comment} =1{' . $spanCount .' Comment} other{' . $spanCount .' Comments}}', ['value' => $commentCount]), null, ['class' => 'user-' . $modelUserPostMain['id'] . '-comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-comments"></i> Comment', null, ['class' => 'user-' . $modelUserPostMain['id'] . '-comments-review-trigger visible-xs']); ?>
    
                                                                </li>
                                                                <li class="review-<?= $modelUserPostMain['id'] ?>-option-toggle visible-xs-inline-block">
                                                                    <i class="fa fa-ellipsis-h"></i>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-5 col-tab-5 text-right visible-lg visible-md visible-sm visible-tab">
                                                            <ul class="list-inline list-review mt-0 mb-0">
                                                                <li>
    
                                                                    <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-review-' . $modelUserPostMain['id'] . '-trigger']); ?>
    
                                                                </li>
                                                            </ul>
                                                    	</div>
                                                        <div class="review-<?= $modelUserPostMain['id'] ?>-option col-xs-12">
                                                            <ul class="list-inline list-review mt-0 mb-0">
                                                                <li>
    
                                                                    <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-review-' . $modelUserPostMain['id'] . '-trigger']); ?>
    
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
    
                                                    <hr class="divider-w mt-10">
    
                                                    <div class="row">
                                                        <div class="user-comment-review" id="user-<?= $modelUserPostMain['id']; ?>-comments-review">
                                                            <div class="col-sm-12">
                                                                <div class="input-group mt-10 mb-10">
                                                                    <span class="input-group-addon" id="basic-addon1"><i class="fa fa-comment"></i></span>
    
                                                                    <?= Html::textInput('comment_input', null, ['id' => 'input-' . $modelUserPostMain['id'] . '-comments-review', 'class' => 'form-control', 'placeholder' => 'Tuliskan komentar']); ?>
    
                                                                </div>
    
                                                                <div class="overlay" style="display: none;"></div>
                                                                <div class="loading-img" style="display: none"></div>
                                                                <div class="comment-<?= $modelUserPostMain['id']; ?>-section">
                                                                    <div class="post-<?= $modelUserPostMain['id']; ?>-comment-container">
    
                                                                        <?php
                                                                        $userReviewComment = [];
    
                                                                        foreach ($modelUserPostMain['userPostComments'] as $dataUserPostComment){
    
                                                                            $userReviewComment[$dataUserPostComment['id']] = $dataUserPostComment;
                                                                        }
    
                                                                        krsort($userReviewComment);
    
                                                                        foreach ($userReviewComment as $dataUserPostComment): ?>
    
                                                                            <div class="comment-post">
                                                                                <div class="row mb-10">
                                                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                                                        <div class="widget">
                                                                                            <div class="widget-comments-image">
    
                                                                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($dataUserPostComment['user']['image']) ? Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-comment-thumb img-component']); ?>
    
                                                                                            </div>
    
                                                                                            <div class="widget-comments-body">
                                                                                                <strong><?= Html::a($dataUserPostComment['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostComment['user']['username']])); ?>&nbsp;&nbsp;&nbsp;</strong>
                                                                                                <small><?= Yii::$app->formatter->asRelativeTime($dataUserPostComment['created_at']) ?></small>
                                                                                                <br>
                                                                                                <p class="review-description">
    
                                                                                                    <?= $dataUserPostComment['text']; ?>
    
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
    
                                                                        <?php
                                                                        endforeach; ?>
    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <hr class="divider-w mb-10">
                                        </div>

                                    <?php
                                    endif; ?>

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
frontend\components\FacebookShare::widget();

$jscript = '
    var photoId = $(".user-post-main-id");

    $(".photo-container").find(".user-" + photoId.val() + "-likes-review-trigger").on("click", function() {
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": photoId.val()
            },
            url: "' . Yii::$app->urlManager->createUrl(['action/submit-likes']) . '",
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt($(".photo-container").find(".user-" + photoId.val() + "-likes-review-trigger").find("span.total-" + photoId.val() + "-likes-review").html());

                    if(response.is_active) {

                        $(".photo-container").find(".user-" + photoId.val() + "-likes-review-trigger").addClass("selected");
                        $(".photo-container").find("span.total-" + photoId.val() + "-likes-review").html((loveValue + 1).toString());
                    } else {

                        $(".photo-container").find(".user-" + photoId.val() + "-likes-review-trigger").removeClass("selected");
                        $(".photo-container").find("span.total-" + photoId.val() + "-likes-review").html((loveValue - 1).toString());
                    }
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });

    $(".photo-container").find(".user-" + photoId.val() + "-comments-review-trigger").on("click", function() {
        $(".photo-container").find("#user-" + photoId.val() + "-comments-review").slideToggle();

        return false;
    });

    $(".photo-container").find("#input-" + photoId.val() + "-comments-review").on("keypress", function(event) {
        if (event.which == 13 && $(this).val().trim()) {
            var data = {
                "user_post_main_id": photoId.val(),
                "text": $(this).val(),
            };

            $.ajax({
                type: "POST",
                data: data,
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {
                    $(".comment-" + photoId.val() + "-section").siblings(".overlay").show();
                    $(".comment-" + photoId.val() + "-section").siblings(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {

                        $("#input-" + response.user_post_main_id + "-comments-review").val("");

                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": response.user_post_main_id
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                            success: function(response) {

                                $(".comment-" + photoId.val() + "-section").html(response);
                            },
                            error: function(xhr, ajaxOptions, thrownError) {

                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                            }
                        });
                    } else {

                        messageResponse(response.icon, response.title, response.message, response.type);
                    }

                    $(".comment-" + response.user_post_main_id + "-section").siblings(".overlay").hide();
                    $(".comment-" + response.user_post_main_id + "-section").siblings(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                }
            });
        }
    });

    $(".review-" + photoId.val() + "-option").hide();

    $(".review-" + photoId.val() + "-option-toggle").on("click", function() {

        $(".review-" + photoId.val() + "-option").slideToggle();
    });

    $(".photo-container").find(".share-review-" + photoId.val() + "-trigger").on("click", function() {

        var url = window.location.href;
        var title = "Foto untuk " + $(".business-name").val();
        var description = $(".photo-container").find(".review-description").text();
        var image = window.location.protocol + "//" + window.location.hostname + $(".photo-container").find(".photo-review").find("img").attr("src");

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