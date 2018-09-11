<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\BaseStringHelper;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;
use kartik\rating\StarRating;

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-recent-post a',
    'options' => ['id' => 'pjax-recent-post-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-recent-post', 'class' => 'pagination'],
]); ?>

<div class="overlay" style="display: none;"></div>
<div class="loading-img" style="display: none"></div>

<div class="row mt-10 mb-20">

    <div class="font-alt mb-20 align-center visible-xs">Recent Activity</div>
    <div class="col-sm-6 font-alt mb-20 align-center visible-tab">Recent Activity</div>
    <div class="col-sm-6 font-alt mb-20 visible-lg visible-md visible-sm">Recent Activity</div>

    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-offset-6 visible-tab text-right p-10">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs m-10">

        <?= $linkPager; ?>

    </div>
</div>

<div class="row">
    <div class="recent-post-container">

        <?php
        foreach ($modelUserPostMain as $dataUserPostMain):

            if ($dataUserPostMain['type'] === 'Review'): ?>

                <?= Html::hiddenInput('user_post_main_id', $dataUserPostMain['id'], ['class' => 'user-post-main-id']) ?>

                <div class="recent-post">
                    <div class="col-xs-12 col-tab-6 col-sm-6 col-md-4 col-lg-4">
                        <div class="box">
                            <div class="post">
                                <div class="head">
                                    <div class="row">
                                        <div class="user-photo col-lg-3 col-xs-3">
                                            <a href="<?= Yii::$app->urlManager->createUrl(['/user/user-profile', 'user' => $dataUserPostMain['user']['username']]); ?>">

                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($dataUserPostMain['user']['image']) ? Tools::thumb('/img/user/', $dataUserPostMain['user']['image'], 100, 100) : '/img/user/default-avatar.png'), [
                                                    'class' => 'img-responsive img-circle img-profile-thumb img-component',
                                                    'height' => 50,
                                                    'width' => 50
                                                ]) ?>

                                            </a>
                                        </div>
                                        <div class="user-name col-lg-9 col-xs-9">
                                            <div class="full-name">

                                                <?= Html::a($dataUserPostMain['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostMain['user']['username']])); ?>

                                            </div>
                                            <div class="created-at">

                                                <small><?= Yii::$app->formatter->asDate($dataUserPostMain['created_at'], 'medium'); ?></small>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-thumbnail">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">

                                            <?php
                                            $url = 'page';

                                            if ($dataUserPostMain['type'] === 'Review') {
                                                $url .= '/review';
                                            } else if ($dataUserPostMain['type'] === 'Photo') {
                                                $url .= '/photo';
                                            }

                                            $img = Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', 'no-image-available.jpg', 360, 135.283));

                                            if ($dataUserPostMain['type'] === 'Photo') {

                                                if (!empty($dataUserPostMain['image']) && file_exists(Yii::getAlias('@uploads') . '/img/user_post/' . $dataUserPostMain['image'])) {
                                                    $img = Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMain['image'], 360, 135.283));
                                                }

                                            } else if ($dataUserPostMain['type'] === 'Review' && !empty($dataUserPostMain['userPostMains'])) {

                                                if (!empty($dataUserPostMain['userPostMains'][0]['image']) && file_exists(Yii::getAlias('@uploads') . '/img/user_post/' . $dataUserPostMain['userPostMains'][0]['image'])) {
                                                    $img= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMain['userPostMains'][0]['image'], 360, 135.283));
                                                }

                                            } ?>

                                            <a href="<?= Yii::$app->urlManager->createUrl([$url, 'id' => $dataUserPostMain['id']]); ?>"> <?= $img; ?> </a>

                                        </div>
                                    </div>
                                </div>
                                <div class="post-header">
                                    <div class="row buttons">
                                        <div class="col-sm-12 col-xs-12 col">
                                            <ul class="list-inline mt-0 mb-10">
                                                <li>

                                                    <small><i class="fa fa-thumbs-up"></i><?= !empty($dataUserPostMain['love_value']) ? ' ' . $dataUserPostMain['love_value'] . ' Likes' : ' ' . 0 . ' Like' ?></small>

                                                </li>
                                                <li>

                                                    <small><i class="fa fa-comments"></i><?= !empty($dataUserPostMain['userPostComments']) ? ' ' . count($dataUserPostMain['userPostComments']) . ' Comments' : ' ' . 0 . ' Comment' ?></small>

                                                </li>
                                                <li>

                                                    <small><?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['id' => 'share-feature', 'class' => 'share-feature-' . $dataUserPostMain['id'] . '-trigger']); ?></small>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row business-name">
                                        <div class="col-sm-12 col-xs-12 col">
                                            <h5 class="font-alt m-0">

                                                <?= Html::a($dataUserPostMain['business']['name'], Yii::$app->urlManager->createUrl(['page/detail', 'id' => $dataUserPostMain['business']['id']])); ?>

                                            </h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-tab-6 col-sm-5 col-xs-5 col">
                                            <div class="widget star-rating">

                                                <?php
                                                $ratingValue = 0;

                                                foreach ($dataUserPostMain['userVotes'] as $dataUserVotes) {
                                                    $ratingValue += !empty($dataUserVotes['vote_value']) && !empty($dataUserPostMain['userVotes']) ? ($dataUserVotes['vote_value'] / count($dataUserPostMain['userVotes'])) : 0;
                                                }

                                                echo StarRating::widget([
                                                    'id' => 'rating-' . $dataUserPostMain['id'],
                                                    'name' => 'rating_' . $dataUserPostMain['id'],
                                                    'value' => !empty($ratingValue) ? $ratingValue : 0,
                                                    'pluginOptions' => [
                                                        'displayOnly' => true,
                                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                        'captionElement' => '.rating-' . strtolower($dataUserPostMain['id']),
                                                        'starCaptions' => new JsExpression('function(val){return val == 0 ? "0 &nbsp;&nbsp;&nbsp; vote" : val + " &nbsp;&nbsp;&nbsp; votes";}'),
                                                        'starCaptionClasses' => new JsExpression('function(val){ return false;}'),
                                                    ]
                                                ]); ?>

                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-tab-6 col-sm-7 col-xs-7 col pb-10">
                                            <div class="rating">
                                                <h4 class="mt-0 mb-0"><span class="label label-success"><?= number_format((float) $ratingValue, 1, '.', '') ?></span></h4>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-6 review-rating-components">
                                            <div class="rating-<?= strtolower($dataUserPostMain['id']) ?>"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-entry">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">

                                            <?= BaseStringHelper::truncate($dataUserPostMain['text'], 85, '. . .'); ?>

                                            <?= (!empty($dataUserPostMain['text']) ? '<br>' : '') . Html::a('<span class="text-red"> View detail <i class="fa fa-angle-double-right"></i></span>', [$url, 'id' => $dataUserPostMain['id']]) ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
            endif;

        endforeach; ?>

    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-offset-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-offset-6 visible-tab text-right p-10">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs m-10">

        <?= $linkPager; ?>

    </div>
</div>

<?php
frontend\components\RatingColor::widget();

$jscript = '

    $(".user-post-main-id").each(function() {
        var thisObj = $(this);

        ratingColor($(".rating"), "span");

        $("#share-feature").on("click", function() {

            url = window.location.href;

            FB.ui({
                method: "share",
                href: url,
            }, function(response){

            });

            return false;
        });

        thisObj.parent().find(".share-feature-" + thisObj.val() + "-trigger").on("click", function() {

            var businessName = $(".business-name").text().trim();
            var rating = thisObj.parent().find(".rating").text().trim();
            var url = window.location.href;

                url = url.replace("detail", "review").replace($("#business_id").val(), thisObj.val());
            var title = "Rating " + rating + " untuk " + businessName;
            var description = thisObj.parent().find(".review-description").text();
            var image = window.location.protocol + "//" + window.location.hostname + thisObj.parent().find("#user-" + thisObj.val() + "-photos-review").eq(0).find(".work-image").children().attr("src");

            FB.ui({
                method: "share_open_graph",
                action_type: "og.likes",
                action_properties: JSON.stringify({
                        object: {
                            "og:url": url,
                            "og:title": title,
                            "og:description": description,
                            "og:image": image
                        }
                })
            },
            function (response) {
                if (response && !response.error_message) {

                    messageResponse("aicon aicon-icon-tick-in-circle", "Sukses.", "Review berhasil di posting ke Facebook Anda.", "success");
                }
            });

            return false;
        });

        $("#pjax-recent-post-container").on("pjax:send", function() {
            $(".recent-post-container").parent().siblings(".overlay").show();
            $(".recent-post-container").parent().siblings(".loading-img").show();
        });

        $("#pjax-recent-post-container").on("pjax:complete", function() {
            $(".recent-post-container").parent().siblings(".overlay").hide();
            $(".recent-post-container").parent().siblings(".loading-img").hide();
        });

        $("#pjax-recent-post-container").on("pjax:error", function (event) {
            event.preventDefault();
        });
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>