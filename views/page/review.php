<?php

use yii\helpers\Html;
use sycomponent\Tools;
use kartik\rating\StarRating;
use common\components\Helper;

/* @var $modelUserPostMain core\models\UserPostMain */

kartik\popover\PopoverXAsset::register($this);

$jspopover = ''; ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail review">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back to Place Detail'), Yii::$app->urlManager->createUrl(['page/detail', 'id' => $modelUserPostMain['business']['id']])) ?>

                </div>
            </div>

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row mt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="box bg-white">
                                <div class="box-content">

                                    <?php
                                    if (!empty($modelUserPostMain)):

                                        $jspopover .= '
                                            $("#user-rating-popover-' . $modelUserPostMain['id'] . '").popoverButton({
                                                trigger: "hover",
                                                placement: "right right-top",
                                                target: "#user-container-popover-' . $modelUserPostMain['id'] . '"
                                            });
                                        ';

                                        $ratingComponent = [];
                                        $totalVoteValue = 0;

                                        foreach ($modelUserPostMain['userVotes'] as $dataUserVote) {

                                            if (!empty($dataUserVote['ratingComponent'])) {

                                                $totalVoteValue += $dataUserVote['vote_value'];

                                                $ratingComponent[$dataUserVote['rating_component_id']] = $dataUserVote;
                                            }
                                        }

                                        $overallValue = !empty($totalVoteValue) && !empty($ratingComponent) ? ($totalVoteValue / count($ratingComponent)) : 0;

                                        ksort($ratingComponent); ?>

                                        <div class="review-container">

                                            <?= Html::hiddenInput('business_name', $modelUserPostMain['business']['name'], ['class' => 'business-name']) ?>

                                            <?= Html::hiddenInput('user_post_main_id', $modelUserPostMain['id'], ['class' => 'user-post-main-id']) ?>

                                            <div class="row mb-10">
                                                <div class="col-md-4 col-sm-5 col-xs-6 visible-lg visible-md visible-sm visible-tab">
                                                    <div class="widget">
                                                        <div class="widget-posts-image">
															
															<?php
															$img = '/img/user/default-avatar.png';
															
															if (!empty($modelUserPostMain['user']['image'])) {
															    
															    $img = Tools::thumb('/img/user/', $modelUserPostMain['user']['image'], 200, 200);
															}
															
															echo Html::img(Yii::getAlias('@uploadsUrl') . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']); ?>

                                                        </div>

                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $modelUserPostMain['user']['username']])); ?>
                                                            <br>
                                                            <small><?= Helper::asRelativeTime($modelUserPostMain['created_at']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-9 visible-xs">
                                                    <div class="widget">
                                                        <div class="widget-posts-image">

                                                            <?php
															$img = '/img/user/default-avatar.png';
															
															if (!empty($modelUserPostMain['user']['image'])) {
															    
															    $img = Tools::thumb('/img/user/', $modelUserPostMain['user']['image'], 200, 200);
															}
															
															echo Html::img(Yii::getAlias('@uploadsUrl') . $img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']); ?>

                                                        </div>

                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $modelUserPostMain['user']['username']])); ?>
                                                            <br>
                                                            <small><?= Helper::asRelativeTime($modelUserPostMain['created_at']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3">

                                                    <h3 class="mt-0 mb-0">
                                                        <div class="rating">

                                                            <?= Html::a(number_format((float) !empty($overallValue) ? $overallValue : 0, 1, '.', ''), null, ['id' => 'user-rating-popover-' . $modelUserPostMain['id'] . '', 'class' => 'label label-success']); ?>

                                                        </div>
                                                    </h3>

                                                    <div id="user-container-popover-<?= $modelUserPostMain['id']; ?>" class="popover popover-x popover-default popover-rating">
                                                        <div class="arrow"></div>
                                                        <div class="popover-header popover-title"><button type="button" class="close" data-dismiss="popover-x">&times;</button></div>
                                                        <div class="popover-body popover-content">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-xs-12">
                                                                    <div class="widget star-rating">
                                                                        <ul class="icon-list">

                                                                            <?php
                                                                            if (!empty($ratingComponent)):

                                                                                foreach ($ratingComponent as $dataUserVote): ?>

                                                                                    <li>
                                                                                        <div class="row">
                                                                                            <div class="col-sm-5 col-xs-5">

                                                                                                <?= StarRating::widget([
                                                                                                    'id' => 'user-' . $modelUserPostMain['id'] . '-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                                                    'name' => 'user-' . $modelUserPostMain['id'] . '-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                                                    'value' => $dataUserVote['vote_value'],
                                                                                                    'pluginOptions' => [
                                                                                                        'displayOnly' => true,
                                                                                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                                                    ]
                                                                                                ]); ?>

                                                                                            </div>

                                                                                            <div class="col-sm-7 col-xs-7">

                                                                                                <?= $dataUserVote['vote_value'] . ' &nbsp;&nbsp;&nbsp;' . $dataUserVote['ratingComponent']['name']; ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </li>

                                                                                <?php
                                                                                endforeach;
                                                                            endif; ?>

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <p class="review-description">

                                                        <?= $modelUserPostMain['text']; ?>

                                                    </p>

                                                    <div class="row" id="user-<?= $modelUserPostMain['id']; ?>-photos-review-container">
                                                        <div class="col-sm-12 col-xs-12">
                                                            <ul class="works-grid works-grid-gut works-grid-5" id="user-<?= $modelUserPostMain['id']; ?>-photos-review">

                                                                <?php
                                                                if (!empty($modelUserPostMain['userPostMains'])):

                                                                    foreach ($modelUserPostMain['userPostMains'] as $modelUserPostMainChild): ?>

                                                                        <li class="work-item gallery-photo-review">
                                                                            <div class="gallery-item post-gallery">
                                                                                <div class="gallery-image">
                                                                                    <a class="gallery" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $modelUserPostMainChild['image']; ?>" title="">
                                                                                        <div class="work-image">

                                                                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $modelUserPostMainChild['image'], 200, 200), ['class' => 'img-component']); ?>

                                                                                        </div>
                                                                                        <div class="work-caption">
                                                                                            <div class="work-descr"><?= !empty($modelUserPostMainChild['text']) ? $modelUserPostMainChild['text'] : '' ?></div>
                                                                                            <div class="work-descr">
                                                                                                <a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $modelUserPostMainChild['image']; ?>"><i class="fa fa-search"></i></a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </li>

                                                                    <?php
                                                                    endforeach;
                                                                endif; ?>

                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <?php                   
                                					$loveCount = !empty($modelUserPostMain['love_value']) ? $modelUserPostMain['love_value'] : 0;
                                					$commentCount = !empty($modelUserPostMain['userPostComments']) ? count($modelUserPostMain['userPostComments']) : 0;
                                					$photoCount = !empty($modelUserPostMain['userPostMains']) ? count($modelUserPostMain['userPostMains']) : 0;
                                					
                                					$loveSpanCount = '<span class="total-' . $modelUserPostMain['id'] . '-likes-review">#</span>'; 
                                					$commentSpanCount = '<span class="total-' . $modelUserPostMain['id'] . '-comments-review">#</span>';
                                					$photoSpanCount = '<span class="total-' . $modelUserPostMain['id'] . '-photos-review">#</span>';
                                					
                                					$selected = !empty($modelUserPostMain['userPostLoves'][0]) ? 'selected' : ''; ?>
                                					
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
                                
                                                                    <small><?= Yii::t('app', '{value, plural, =0{' . $commentSpanCount .' Comment} =1{' . $commentSpanCount .' Comment} other{' . $commentSpanCount .' Comments}}', ['value' => $commentCount]) ?></small>
                                
                                                                </li>
                                                                <li>
                                									
                                                                    <small><?= Yii::t('app', '{value, plural, =0{' . $photoSpanCount .' Comment} =1{' . $photoSpanCount .' Comment} other{' . $photoSpanCount .' Comments}}', ['value' => $photoCount]) ?></small>
                                
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                
                                                    <div class="row">
                                                        <div class="col-sm-7 col-tab-7 col-xs-12">
                                                            <ul class="list-inline list-review mt-0 mb-0">
                                                                <li>
                                
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> ' . Yii::t('app', '{value, plural, =0{' . $loveSpanCount .' Like} =1{' . $loveSpanCount .' Like} other{' . $loveSpanCount .' Likes}}', ['value' => $loveCount]), null , ['class' => 'user-' . $modelUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', null, ['class' => 'user-' . $modelUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-xs']); ?>
                                
                                                                </li>
                                                                <li>
                                
                                                                    <?= Html::a('<i class="fa fa-comments"></i> ' . Yii::t('app', '{value, plural, =0{' . $commentSpanCount .' Comment} =1{' . $commentSpanCount .' Comment} other{' . $commentSpanCount .' Comments}}', ['value' => $commentCount]), null, ['class' => 'user-' . $modelUserPostMain['id'] . '-comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-comments"></i> Comment', null, ['class' => 'user-' . $modelUserPostMain['id'] . '-comments-review-trigger visible-xs']); ?>
                                
                                                                </li>
                                                                <li>
                                									
                                                                    <?= Html::a('<i class="fa fa-camera-retro"></i> ' . Yii::t('app', '{value, plural, =0{' . $photoSpanCount .' Photo} =1{' . $photoSpanCount .' Photo} other{' . $photoSpanCount .' Photos}}', ['value' => $photoCount]), null, ['class' => 'user-' . $modelUserPostMain['id'] . '-photos-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-camera-retro"></i> Photo', null, ['class' => 'user-' . $modelUserPostMain['id'] . '-photos-review-trigger visible-xs']); ?>
                                
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
                                                                <div class="loading-img" style="display: none;"></div>
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

                                                                                                <?php
                                                                                                $img = '/img/user/default-avatar.png';
                                                                                                if (!empty($dataUserPostComment['user']['image'])) {
                                                                                                    
                                                                                                    $img = Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200);
                                                                                                }
                                                                                                
                                                                                                echo Html::img(Yii::getAlias('@uploadsUrl') . $img, ['class' => 'img-responsive img-circle img-comment-thumb img-component']); ?>

                                                                                            </div>

                                                                                            <div class="widget-comments-body">
                                                                                                <strong><?= Html::a($dataUserPostComment['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostComment['user']['username']])); ?>&nbsp;&nbsp;&nbsp;</strong>
                                                                                                <small><?= Helper::asRelativeTime($dataUserPostComment['created_at']) ?></small>
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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\Readmore::widget();
frontend\components\FacebookShare::widget();

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var reviewId = $(".user-post-main-id");

    $(".review-container").find(".user-" + reviewId.val() + "-likes-review-trigger").on("click", function() {

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": reviewId.val()
            },
            url: "' . Yii::$app->urlManager->createUrl(['action/submit-likes']) . '",
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt($(".review-container").find(".user-" + reviewId.val() + "-likes-review-trigger").find("span.total-" + reviewId.val() + "-likes-review").html());

                    if (response.is_active) {

                        $(".review-container").find(".user-" + reviewId.val() + "-likes-review-trigger").addClass("selected");
                        $(".review-container").find("span.total-" + reviewId.val() + "-likes-review").html((loveValue + 1).toString());
                    } else {

                        $(".review-container").find(".user-" + reviewId.val() + "-likes-review-trigger").removeClass("selected");
                        $(".review-container").find("span.total-" + reviewId.val() + "-likes-review").html((loveValue - 1).toString());
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

    $(".review-container").find(".user-" + reviewId.val() + "-comments-review-trigger").on("click", function() {

        $(".review-container").find("#user-" + reviewId.val() + "-comments-review").slideToggle();

        return false;
    });

    $(".review-container").find("#input-" + reviewId.val() + "-comments-review").on("keypress", function(event) {

        if (event.which == 13 && $(this).val().trim()) {

            var data = {
                "user_post_main_id": reviewId.val(),
                "text": $(this).val(),
            };

            $.ajax({
                cache: false,
                type: "POST",
                data: data,
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {
                    $(".comment-" + reviewId.val() + "-section").siblings(".overlay").show();
                    $(".comment-" + reviewId.val() + "-section").siblings(".loading-img").show();
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

                                $(".comment-" + reviewId.val() + "-section").html(response);
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

    if ($(".review-container").find("#user-" + reviewId.val() + "-photos-review").find(".gallery-photo-review").length) {

        $(".review-container").find(".user-" + reviewId.val() + "-photos-review-trigger").on("click", function() {

            $(".review-container").find("#user-" + reviewId.val() + "-photos-review-container").toggle(500);

            return false;
        });
    }

    $(".review-container").find("#user-" + reviewId.val() + "-photo-review, .post-gallery").magnificPopup({

        delegate: "a.show-image",
        type: "image",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        },
        image: {
            titleSrc: "title",
            tError: "The image could not be loaded."
        }
    });

    $(".review-" + reviewId.val() + "-option").hide();

    $(".review-" + reviewId.val() + "-option-toggle").on("click", function() {

        $(".review-" + reviewId.val() + "-option").slideToggle();
    });

    $(".review-container").find(".share-review-" + reviewId.val() + "-trigger").on("click", function() {

        var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review', 'id' => $modelUserPostMain['id']]) . '";
        var title = "Rating " + $(".review-container").find(".rating").text().trim() + " untuk " + $(".business-name").val();
        var description = $(".review-container").find(".review-description").text();
        var image = window.location.protocol + "//" + window.location.hostname + $(".review-container").find("#user-" + reviewId.val() + "-photos-review").eq(0).find(".work-image").children().attr("src");

        facebookShare({
            ogUrl: url,
            ogTitle: title,
            ogDescription: description,
            ogImage: image,
            type: "Review"
        });

        return false;
    });

    ratingColor($(".rating"), "a");

    readmoreText({
        element: $(".review-description"),
        minChars: 500,
        ellipsesText: " . . . ",
        moreText: "See more",
        lessText: "See less",
    });
';

$this->registerJs($jscript . $jspopover); ?>