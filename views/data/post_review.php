<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use kartik\rating\StarRating;
use common\components\Helper;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelUserPostMain core\models\UserPostMain */

kartik\popover\PopoverXAsset::register($this);

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-review a',
    'options' => ['id' => 'pjax-review-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-review', 'class' => 'pagination'],
]);

$jspopover = ''; ?>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

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

<div class="row" style="position: relative;">
	<div class="post-review-container">
	
		<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <?php
        if (!empty($modelUserPostMain)):
        
            foreach ($modelUserPostMain as $dataUserPostMain):
        
                $jspopover .= '
                    $("#user-rating-popover' . $dataUserPostMain['id'] . '").popoverButton({
                        trigger: "hover",
                        placement: "right right-top",
                        target: "#user-container-popover' . $dataUserPostMain['id'] . '"
                    });
                ';
        
                $img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';
        
                if (!empty($dataUserPostMain['user']['image'])) {
        
                    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', $dataUserPostMain['user']['image'], 200, 200);
                }
                
                $img = Html::img($img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']);
        
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
        
                    foreach ($dataUserPostMain['userPostComments'] as $dataUserPostComment) {
        
                        $userReviewComment[$dataUserPostComment['id']] = $dataUserPostComment;
                    }
                }
        
                $overallValue = !empty($totalVoteValue) && !empty($ratingComponent) ? ($totalVoteValue / count($ratingComponent)) : 0;
        
                ksort($ratingComponent);
                ksort($userReviewComment);
        
                $layoutUser = '
                    <div class="widget-posts-image">
                        ' . Html::a($img, ['user/user-profile', 'user' => $dataUserPostMain['user']['username']]) . '
                    </div>
        
                    <div class="widget-posts-body">
                        ' . Html::a($dataUserPostMain['user']['full_name'], ['user/user-profile', 'user' => $dataUserPostMain['user']['username']]) . '
                        <br>
                        <small>' . Helper::asRelativeTime($dataUserPostMain['updated_at']) . '</small>
                    </div>
                '; ?>
        
                <div class="col-lg-12 review-post">
        
                    <?= Html::hiddenInput('user_post_main_id', $dataUserPostMain['id'], ['class' => 'user-post-main-id']) ?>
        
                    <div class="row mb-10">
                        <div class="col-md-4 col-sm-5 col-xs-6 visible-lg visible-md visible-sm visible-tab">
                            <div class="widget">
                                <?= $layoutUser ?>
                            </div>
                        </div>
                        <div class="col-xs-9 visible-xs">
                            <div class="widget">
                                <?= $layoutUser ?>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3">
                            <div class="rating">
                            	<h3 class="mt-0 mb-0">
                                    <?= Html::a(number_format(!empty($overallValue) ? $overallValue : 0, 1), '#', ['id' => 'user-rating-popover' . $dataUserPostMain['id'] . '', 'class' => 'label label-success']); ?>
                            	</h3>
                            </div>
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
        
                                                                        <?= ' ' . $dataUserVote['vote_value'] . ' &nbsp;&nbsp;&nbsp;' . $dataUserVote['ratingComponent']['name']; ?>
        
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
        
                                <?= $dataUserPostMain['text']; ?>
        
                            </p>
                        </div>
                    </div>
        
                    <div class="row" id="user-<?= $dataUserPostMain['id']; ?>-photos-review">
                        <div class="col-sm-12 col-xs-12">
                            <ul class="works-grid works-grid-gut works-grid-5">
        
                                <?php
                                if (!empty($dataUserPostMain['userPostMains'])):
        
                                    foreach ($dataUserPostMain['userPostMains'] as $dataUserPostMainChild): ?>
        
                                        <li class="work-item gallery-photo-review">
                                            <div class="gallery-item post-gallery">
                                                <div class="gallery-image">
                                                    <div class="work-image">
        
                                                        <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMainChild['image'], 200, 200), ['class' => 'img-component']); ?>
        
                                                    </div>
                                                    <div class="work-caption">
                                                        <div class="work-descr">
                                                            <a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $dataUserPostMainChild['image']; ?>"><i class="fa fa-search"></i></a>
                                                        </div>
                                                    </div>
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
                    $loveCount = !empty($dataUserPostMain['love_value']) ? $dataUserPostMain['love_value'] : 0;
                    $commentCount = !empty($dataUserPostMain['userPostComments']) ? count($dataUserPostMain['userPostComments']) : 0;
                    $photoCount = !empty($dataUserPostMain['userPostMains']) ? count($dataUserPostMain['userPostMains']) : 0;
                    
                    $loveSpanCount = '<span class="total-' . $dataUserPostMain['id'] . '-likes-review">#</span>';
                    $commentSpanCount = '<span class="total-' . $dataUserPostMain['id'] . '-comments-review">#</span>';
                    $photoSpanCount = '<span class="total-' . $dataUserPostMain['id'] . '-photos-review">#</span>';
                    
                    $selected = !empty($dataUserPostMain['userPostLoves'][0]) ? 'selected' : 0; ?>
        
                    <div class="row visible-xs">
                        <div class="col-xs-3">
                            <ul class="list-inline mt-0 mb-0">
                                <li>
                                    <small><?= '<i class="fa fa-thumbs-up"></i> <span class="total-' . $dataUserPostMain['id'] . '-likes-review">' . $loveCount . '</span>' ?></small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-9 text-right">
                            <ul class="list-inline mt-0 mb-0">
                                <li>
                                    <small><?= Yii::t('app', '{value, plural, =0{' . $commentSpanCount . ' Comment} =1{' . $commentSpanCount . ' Comment} other{' . $commentSpanCount . ' Comments}}', ['value' => $commentCount]) ?></small>
                                </li>
                                <li>
                                    <small><?= Yii::t('app', '{value, plural, =0{' . $photoSpanCount . ' Photo} =1{' . $photoSpanCount . ' Photo} other{' . $photoSpanCount . ' Photos}}', ['value' => $photoCount]) ?></small>
                                </li>
                            </ul>
                        </div>
                    </div>
        
                    <div class="row">
                        <div class="col-sm-7 col-tab-7 col-xs-12">
                            <ul class="list-inline list-review mt-0 mb-0">
                                <li>
                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> ' . Yii::t('app', '{value, plural, =0{' . $loveSpanCount . ' Like} =1{' . $loveSpanCount . ' Like} other{' . $loveSpanCount . ' Likes}}', ['value' => $loveCount]), ['action/submit-likes'], ['class' => 'user-' . $dataUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', ['action/submit-likes'], ['class' => 'user-' . $dataUserPostMain['id'] . '-likes-review-trigger ' . $selected . ' visible-xs']); ?>
                                </li>
                                <li>
                                    <?= Html::a('<i class="fa fa-comments"></i> ' . Yii::t('app', '{value, plural, =0{' . $commentSpanCount . ' Comment} =1{' . $commentSpanCount . ' Comment} other{' . $commentSpanCount . ' Comments}}', ['value' => $commentCount]), '', ['class' => 'user-' . $dataUserPostMain['id'] . '-comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                    <?= Html::a('<i class="fa fa-comments"></i> Comment', '', ['class' => 'user-' . $dataUserPostMain['id'] . '-comments-review-trigger visible-xs']); ?>
                                </li>
                                <li>
                                    <?= Html::a('<i class="fa fa-camera-retro"></i> ' . Yii::t('app', '{value, plural, =0{' . $photoSpanCount . ' Photo} =1{' . $photoSpanCount . ' Photo} other{' . $photoSpanCount . ' Photos}}', ['value' => $photoCount]), '', ['class' => 'user-' . $dataUserPostMain['id'] . '-photos-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                    <?= Html::a('<i class="fa fa-camera-retro"></i> Photo', '', ['class' => 'user-' . $dataUserPostMain['id'] . '-photos-review-trigger visible-xs']); ?>
                                </li>
                                <li class="visible-xs-inline-block">
                                    <?= Html::a('<i class="fa fa-share-alt"></i> Share', '', ['class' => 'share-review-' . $dataUserPostMain['id'] . '-trigger']); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-5 col-tab-5 text-right visible-lg visible-md visible-sm visible-tab">
                            <ul class="list-inline list-review mt-0 mb-0">
                                <li>
                                    <?= Html::a('<i class="fa fa-share-alt"></i> Share', '', ['class' => 'share-review-' . $dataUserPostMain['id'] . '-trigger']); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
        
                    <hr class="divider-w mt-10">
        
                    <div class="row">
                        <div class="user-comment-review" id="user-<?= $dataUserPostMain['id']; ?>-comments-review">
                            <div class="col-sm-12">
                                <div class="input-group mt-10 mb-10">
                                    <span class="input-group-addon"><i class="fa fa-comment"></i></span>
        
                                    <?= Html::textInput('comment_input', null, [
                                        'id' => 'input-' . $dataUserPostMain['id'] . '-comments-review', 
                                        'class' => 'form-control', 
                                        'placeholder' => Yii::t('app', 'Write a Comment')]); ?>
        
                                </div>
        
                                <div class="overlay" style="display: none;"></div>
                                <div class="loading-img" style="display: none;"></div>
                                
                                <div class="comment-<?= $dataUserPostMain['id']; ?>-section">
                                    <div class="comment-container">
        
                                        <?php
                                        if (!empty($userReviewComment)):
        
                                            foreach ($userReviewComment as $dataUserPostComment): ?>
        
                                                <div class="comment-post">
                                                    <div class="row mb-10">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <div class="widget">
                                                                <div class="widget-comments-image">
        
                                                                    <?php
                                                                    $img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';
    
                                                                    if (!empty($dataUserPostComment['user']['image'])) {
    
                                                                        $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200);
                                                                    }
    
                                                                    $img = Html::img($img, ['class' => 'img-responsive img-circle img-comment-thumb img-component']);
                                                                    
                                                                    echo Html::a($img, ['user/user-profile', 'user' => $dataUserPostComment['user']['username']]); ?>
                                                                    
                                                                </div>
        
                                                                <div class="widget-comments-body">
                                                                    <?= Html::a($dataUserPostComment['user']['full_name'], ['user/user-profile', 'user' => $dataUserPostComment['user']['username']]); ?>&nbsp;&nbsp;&nbsp;
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
        
                    <hr class="divider-w mb-10">
                </div>
        
            <?php
            endforeach;
        endif; ?>
        
    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

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
$jscript = '    
    $(".user-post-main-id").each(function() {

        var thisObj = $(this);

        thisObj.parent().find("#user-" + thisObj.val() + "-comments-review").hide();
        thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").hide();

        
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

        thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").on("click", function() {

            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "user_post_main_id": thisObj.val()
                },
                url: $(this).attr("href"),
                success: function(response) {

                    if (response.success) {

                        var loveValue = parseInt($(".total-" + thisObj.val() + "-likes-review").html());

                        if (response.is_active) {

                            thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").addClass("selected");
                            $(".total-" + thisObj.val() + "-likes-review").html(loveValue + 1);
                        } else {

                            thisObj.parent().find(".user-" + thisObj.val() + "-likes-review-trigger").removeClass("selected");
                            $(".total-" + thisObj.val() + "-likes-review").html(loveValue - 1);
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

        thisObj.parent().find(".user-" + thisObj.val() + "-comments-review-trigger").on("click", function() {

            thisObj.parent().find("#user-" + thisObj.val() + "-comments-review").slideToggle();
            thisObj.parent().find("#input-" + thisObj.val() + "-comments-review").trigger("focus");

            return false;
        });

        thisObj.parent().find("#input-" + thisObj.val() + "-comments-review").on("keypress", function(event) {

            if (event.which == 13 && $(this).val().trim()) {

                $.ajax({
                    cache: false,
                    type: "POST",
                    data: {
                        "user_post_main_id": thisObj.val(),
                        "text": $(this).val(),
                    },
                    url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                    beforeSend: function(xhr) {

                        $(".comment-" + thisObj.val() + "-section").siblings(".overlay").show();
                        $(".comment-" + thisObj.val() + "-section").siblings(".loading-img").show();
                    },
                    success: function(response) {

                        if (response.success) {

                            $("#input-" + thisObj.val() + "-comments-review").val("");

                            $.ajax({
                                cache: false,
                                type: "POST",
                                data: {
                                    "user_post_main_id": thisObj.val()
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

                        $(".comment-" + thisObj.val() + "-section").siblings(".overlay").hide();
                        $(".comment-" + thisObj.val() + "-section").siblings(".loading-img").hide();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                        messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                    }
                });
            }
        });

        thisObj.parent().find(".user-" + thisObj.val() + "-photos-review-trigger").on("click", function() {

            if (thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").find(".gallery-photo-review").length) {
            
                thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").toggle(500);
            }

            return false;
        });

        thisObj.parent().find(".share-review-" + thisObj.val() + "-trigger").on("click", function() {

            var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + thisObj.val();
            var title = "Rating " + thisObj.parent().find(".rating").text().trim() + " untuk " + $(".business-name").text().trim();
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

    $(".total-review").html(parseInt($(".total-review").html()) + ' . $totalCount . ');

    ratingColor($(".rating"), "a");

    readmoreText({
        element: $(".review-description"),
        minChars: 500,
        ellipsesText: " . . . ",
        moreText: "See more",
        lessText: "See less",
    });

    $("#pjax-review-container").on("pjax:send", function() {

        $(".post-review-container").children(".overlay").show();
        $(".post-review-container").children(".loading-img").show();
    });

    $("#pjax-review-container").on("pjax:complete", function() {

        $(".post-review-container").children(".overlay").hide();
        $(".post-review-container").children(".loading-img").hide();
    });

    $("#pjax-review-container").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript . $jspopover);

Pjax::end(); ?>