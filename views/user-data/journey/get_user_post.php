<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use common\components\Helper;
use sycomponent\Tools;
use kartik\rating\StarRating;

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-user-post a',
    'options' => ['id' => 'pjax-user-post-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-user-post', 'class' => 'pagination'],
]);

kartik\popover\PopoverXAsset::register($this); ?>

<div class="overlay" style="display: none;"></div>
<div class="loading-img" style="display: none"></div>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing ') . $startItem . ' - ' . $endItem . Yii::t('app', ' OF ') . $totalCount . ' ' . Yii::t('app', 'Results'); ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
$jspopover = '';

if (!empty($modelUserPostMain)):

    foreach ($modelUserPostMain as $dataUserPostMain):

        $jspopover .= '
            $("#user-rating-popover' . $dataUserPostMain['id'] . '").popoverButton({
                trigger: "hover",
                placement: "right right-top",
                target: "#user-container-popover' . $dataUserPostMain['id'] . '"
            });
        ';

        $imgBusinessProfile = Yii::$app->urlManager->baseUrl . '/media/img/no-image-available-60-60.jpg';

        if (!empty($dataUserPostMain['business']['businessImages'][0]['image'])) {

            $imgBusinessProfile = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataUserPostMain['business']['businessImages'][0]['image'], 60, 60);
        }

        $totalVoteValue = 0;
        $ratingComponent = [];
        $userReviewComment = [];

        if (!empty($dataUserPostMain['userVotes'])) {

            foreach ($dataUserPostMain['userVotes'] as $dataUserVote) {

                if (!empty($dataUserVote['ratingComponent'])) {

                    $totalVoteValue += $dataUserVote['vote_value'];

                    $ratingComponent[$dataUserVote['rating_component_id']] = $dataUserVote;
                }
            }
        }

        if (!empty($dataUserPostMain['userPostComments'])) {

            foreach ($dataUserPostMain['userPostComments'] as $dataUserPostComment){

                $userReviewComment[$dataUserPostComment['id']] = $dataUserPostComment;
            }
        }

        $overallValue = !empty($totalVoteValue) && !empty($ratingComponent) ? ($totalVoteValue / count($ratingComponent)) : 0;

        ksort($ratingComponent);
        krsort($userReviewComment);

        $layoutUser = '
            <div class="widget-posts-image">
                <a href="' . Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataUserPostMain['business']['id']]) . '">

                    ' . Html::img($imgBusinessProfile, ['class' => 'img-responsive img-rounded img-place-thumb img-component']) . '

                </a>
            </div>

            <div class="widget-posts-body">
                ' . Html::a($dataUserPostMain['business']['name'], ['page/detail', 'id' => $dataUserPostMain['business']['id']]) . '
                <br>
                <small>' . Helper::asRelativeTime($dataUserPostMain['created_at']) . '</small>
            </div>
        ' ?>

            <div class="review-post">

                <?= Html::hiddenInput('user_post_main_id', $dataUserPostMain['id'], ['class' => 'user-post-main-id']) ?>

                <?= Html::hiddenInput('business_name', $dataUserPostMain['business']['name'], ['class' => 'business-name']) ?>

                <div class="row mb-10">
                    <div class="col-md-4 col-sm-5 col-xs-6 visible-lg visible-md visible-sm visible-tab">
                        <div class="widget img-place">

                            <?= $layoutUser ?>

                        </div>
                    </div>
                    <div class="col-xs-9 visible-xs">
                        <div class="widget">

                            <?= $layoutUser ?>

                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">

                        <h3 class="mt-0 mb-0">
                            <div class="rating">

                                <?= Html::a(number_format((float) !empty($overallValue) ? $overallValue : 0, 1, '.', ''), null, ['id' => 'user-rating-popover' . $dataUserPostMain['id'] . '', 'class' => 'label label-success']); ?>

                            </div>
                        </h3>

                        <div id="user-container-popover<?= $dataUserPostMain['id']; ?>" class="popover popover-x popover-default popover-rating">
                            <div class="arrow"></div>
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
                                                                        'id' => 'user-' . $dataUserPostMain['id'] . '-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                        'name' => 'user-' . $dataUserPostMain['id'] . '-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                        'value' => $dataUserVote['vote_value'],
                                                                        'pluginOptions' => [
                                                                            'displayOnly' => true,
                                                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                        ]
                                                                    ]); ?>

                                                                </div>

                                                                <div class="col-sm-7 col-xs-7">

                                                                    <?= $dataUserVote['vote_value'] . ' ' . $dataUserVote['ratingComponent']['name']; ?>

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

                            <?= $dataUserPostMain['text'] ?>

                        </p>

                        <div class="row" id="user-<?= $dataUserPostMain['id']; ?>-photos-review">
                            <div class="col-sm-12 col-xs-12">
                                <ul class="works-grid works-grid-gut works-grid-5">

                                    <?php
                                    if (!empty($dataUserPostMain['userPostMains'])):

                                        foreach ($dataUserPostMain['userPostMains'] as $dataUserPostMainChild): ?>

                                            <li class="work-item gallery-photo-review">
                                                <div class="gallery-item post-gallery">
                                                    <div class="gallery-image">
                                                        <a class="gallery" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $dataUserPostMainChild['image']; ?>" title="">
                                                            <div class="work-image">

                                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMainChild['image'], 200, 200), ['class' => 'img-component']); ?>

                                                            </div>
                                                            <div class="work-caption">
                                                                <div class="work-descr"><?= !empty($dataUserPostMainChild['text']) ? $dataUserPostMainChild['text'] : '' ?></div>
                                                                <div class="work-descr">
                                                                    <a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $dataUserPostMainChild['image']; ?>"><i class="fa fa-search"></i></a>
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

                        <div class="row visible-xs">
                            <div class="col-xs-3">
                                <ul class="list-inline mt-0 mb-0">
                                    <li>

                                        <small><?= '<i class="fa fa-thumbs-up"></i> <span class="total-' . $dataUserPostMain['id'] . '-likes-review">' . $dataUserPostMain['love_value'] . '</span>' ?></small>

                                    </li>
                                </ul>
                            </div>
                            <div class="col-xs-9 text-right">
                                <ul class="list-inline mt-0 mb-0">
                                    <li>

                                        <small><?= '<span class="total-' . $dataUserPostMain['id'] . '-comments-review">' . count($dataUserPostMain['userPostComments']) . '</span> Comment' ?></small>

                                    </li>
                                    <li>

                                        <small><?= '<span class="total-' . $dataUserPostMain['id'] . '-photos-review">' . count($dataUserPostMain['userPostMains']) . '</span> Photo' ?></small>

                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-7 col-tab-7 col-xs-12">
                                <ul class="list-inline list-review mt-0 mb-0">
                                    <li>

                                        <?php
                                        $selected = '';

                                        if (!empty($dataUserPostMain['userPostLoves'][0])) {
                                            $selected = 'selected';
                                        } ?>

                                        <?= Html::a('<i class="fa fa-thumbs-up"></i> <span class="total-' . $dataUserPostMain['id'] . '-likes-review">' . $dataUserPostMain['love_value'] . '</span> Like', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                        <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-xs']); ?>

                                    </li>
                                    <li>

                                        <?= Html::a('<i class="fa fa-comments"></i> <span class="total-' . $dataUserPostMain['id'] . '-comments-review">' . count($dataUserPostMain['userPostComments']) . '</span> Comment', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                        <?= Html::a('<i class="fa fa-comments"></i> Comment', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-comments-review-trigger visible-xs']); ?>

                                    </li>
                                    <li>

                                        <?= Html::a('<i class="fa fa-camera-retro"></i> <span class="total-' . $dataUserPostMain['id'] . '-photos-review">' . count($dataUserPostMain['userPostMains']) . '</span> Photo', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-photos-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                        <?= Html::a('<i class="fa fa-camera-retro"></i> Photo', null, ['class' => 'user-' . $dataUserPostMain['id'] . '-photos-review-trigger visible-xs']); ?>

                                    </li>
                                    <li class="review-<?= $dataUserPostMain['id'] ?>-option-toggle visible-xs-inline-block">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-5 col-tab-5 text-right visible-lg visible-md visible-sm visible-tab">
                                <ul class="list-inline list-review mt-0 mb-0">
                                    <li>

                                        <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-review-' . $dataUserPostMain['id'] . '-trigger']); ?>

                                    </li>

                                    <?php
                                    if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->id == $dataUserPostMain['user_id']): ?>

                                        <li>

                                            <?= Html::a('<i class="fa fa-trash"></i> Delete', ['user-action/delete-user-post', 'id' => $dataUserPostMain['id']], ['class' => 'user-' . $dataUserPostMain['id'] . '-delete-review-trigger']) ?>

                                        </li>

                                    <?php
                                    endif; ?>

                                </ul>
                            </div>
                            <div class="review-<?= $dataUserPostMain['id'] ?>-option col-xs-12">
                                <ul class="list-inline list-review mt-0 mb-0">
                                    <li>

                                        <?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-review-' . $dataUserPostMain['id'] . '-trigger']); ?>

                                    </li>

                                    <?php
                                    if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->id == $dataUserPostMain['user_id']): ?>

                                        <li>

                                            <?= Html::a('<i class="fa fa-trash"></i> Delete', ['user-action/delete-user-post', 'id' => $dataUserPostMain['id']], ['class' => 'user-' . $dataUserPostMain['id'] . '-delete-review-trigger']) ?>

                                        </li>

                                    <?php
                                    endif; ?>

                                </ul>
                            </div>
                        </div>

                        <hr class="divider-w mt-10">

                        <div class="row">
                            <div class="user-comment-review" id="user-<?= $dataUserPostMain['id']; ?>-comments-review">
                                <div class="col-sm-12">
                                    <div class="input-group mt-10 mb-10">
                                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-comment"></i></span>

                                        <?= Html::textInput('comment_input', null, ['id' => 'input-' . $dataUserPostMain['id'] . '-comments-review', 'class' => 'form-control', 'placeholder' => 'Tuliskan komentar']); ?>

                                    </div>

                                    <div class="overlay" style="display: none;"></div>
                                    <div class="loading-img" style="display: none"></div>
                                    <div class="comment-<?= $dataUserPostMain['id']; ?>-section">
                                        <div class="post-<?= $dataUserPostMain['id']; ?>-comment-container">

                                            <?php
                                            if (!empty($userReviewComment)):

                                                foreach ($userReviewComment as $dataUserPostComment): ?>

                                                    <div class="comment-post">
                                                        <div class="row mb-10">
                                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                                <div class="widget">
                                                                    <div class="widget-comments-image">

                                                                        <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($dataUserPostComment['user']['image']) ? Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-comment-thumb img-component']); ?>

                                                                    </div>

                                                                    <div class="widget-comments-body">
                                                                        <strong><?= $dataUserPostComment['user']['full_name']; ?>&nbsp;&nbsp;&nbsp;</strong>
                                                                        <small><?= Helper::asRelativeTime($dataUserPostComment['created_at']) ?></small>
                                                                        <br>
                                                                        <p class="comment-description">

                                                                            <?= $dataUserPostComment['text']; ?>

                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php
                                                endforeach;
                                            endif; ?>

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
    endforeach;
endif;?>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing ') . $startItem . ' - ' . $endItem . Yii::t('app', ' OF ') . $totalCount . ' ' . Yii::t('app', 'Results'); ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
frontend\components\GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\Readmore::widget();
frontend\components\FacebookShare::widget();

$jscript = '
    $("#pjax-user-post-container").on("pjax:send", function() {
        $(".review-post").siblings(".overlay").show();
        $(".review-post").siblings(".loading-img").show();
    });

    $("#pjax-user-post-container").on("pjax:complete", function() {
        $(".review-section").siblings(".overlay").hide();
        $(".review-section").siblings(".loading-img").hide();
    });

    $("#pjax-user-post-container").on("pjax:error", function (event) {
        event.preventDefault();
    });

    $(".total-user-post").html("' . $totalCount . '");

    $(".user-post-main-id").each(function() {
        var thisObj = $(this);

        thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").on("click", function(){
            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "user_post_main_id": thisObj.val()
                },
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-likes']) . '",
                success: function(response) {

                    if (response.status == "sukses") {

                        var loveValue = parseInt(thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").find("span.total-" + thisObj.val() + "-likes-review").html());

                        if(response.is_active) {

                            thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").addClass("selected");
                            thisObj.parent().find("span.total-" + thisObj.val() + "-likes-review").html((loveValue + 1).toString());
                        } else {

                            thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").removeClass("selected");
                            thisObj.parent().find("span.total-" + thisObj.val() + "-likes-review").html((loveValue - 1).toString());
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

        thisObj.parent().find("#user-" + thisObj.val() + "-comments-review").hide();
        thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").hide();

        thisObj.parent().find(".user-" + thisObj.val() + "-comments-review-trigger").on("click", function(){
            thisObj.parent().find("#user-" + thisObj.val() + "-comments-review").slideToggle();

            return false;
        });

        thisObj.parent().find("#input-" + thisObj.val() + "-comments-review").on("keypress", function(event) {
            if(event.which == 13 && $(this).val().trim()) {
                var data = {
                    "user_post_main_id": thisObj.val(),
                    "text": $(this).val(),
                };

                $.ajax({
                    type: "POST",
                    data: data,
                    url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                    beforeSend: function(xhr) {
                        $(".comment-" + thisObj.val() + "-section").siblings(".overlay").show();
                        $(".comment-" + thisObj.val() + "-section").siblings(".loading-img").show();
                    },
                    success: function(response) {

                        if (response.status == "sukses") {

                            $("#input-" + response.user_post_main_id + "-comments-review").val("");

                            $.ajax({
                                cache: false,
                                type: "POST",
                                data: {
                                    "user_post_main_id": response.user_post_main_id
                                },
                                url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                                success: function(response) {

                                    $(".comment-" + thisObj.val() + "-section").html(response);
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

        if (thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").find(".gallery-photo-review").length) {
            thisObj.parent().find(".user-" + thisObj.val() + "-photos-review-trigger").on("click", function(){
                thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").toggle(500);

                return false;
            });
        }

        thisObj.parent().find("#user-" + thisObj.val() + "-photo-review, .post-gallery").magnificPopup({
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

        $(".review-" + thisObj.val() + "-option").hide();

        $(".review-" + thisObj.val() + "-option-toggle").on("click", function() {

            $(".review-" + thisObj.val() + "-option").slideToggle();
        });

        thisObj.parent().find(".user-" + thisObj.val() + "-delete-review-trigger").on("click", function() {

            var form = $("form#rating-popover-form-" + thisObj.val()).serialize();

            $.ajax({
                cache: false,
                type: "POST",
                data: form,
                url: $(this).attr("href"),
                success: function(response) {

                    if (response.status == "sukses") {

                        var totalUserPost = parseInt($(".total-user-post").html());

                        if (response.publish) {

                            $(".user-" + response.userPostId + "-delete-review-trigger").html("<i class=\"fa fa-trash-alt\"></i> Delete").attr("href", response.deleteUrlReview);
                            $(".total-user-post").html(totalUserPost + 1);
                        } else {

                            $(".user-" + response.userPostId + "-delete-review-trigger").html("<i class=\"fa fa-undo-alt\"></i> Undo").attr("href", response.undoUrlReview);
                            $(".total-user-post").html(totalUserPost - 1);
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

        thisObj.parent().find(".share-review-" + thisObj.val() + "-trigger").on("click", function() {

            var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + thisObj.val();
            var title = "Rating " + thisObj.parent().find(".rating").text().trim() + " untuk " + thisObj.parent().find(".business-name").val();
            var description = thisObj.parent().find(".review-description").text();
            var image = window.location.protocol + "//" + window.location.hostname + thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").eq(0).find(".work-image").children().attr("src");

            facebookShare({
                ogUrl: url,
                ogTitle: title,
                ogDescription: description,
                ogImage: image,
                type: "Review"
            });

            return false;
        });
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

$this->registerJs($jscript . $jspopover);

Pjax::end(); ?>