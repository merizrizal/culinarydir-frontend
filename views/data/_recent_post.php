<?php
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\StringHelper;
use sycomponent\Tools;
use kartik\rating\StarRating; ?>

<?= Html::hiddenInput('user_post_main_id', $model['id'], ['class' => 'user-post-main-id']) ?>

<div class="col-xs-12 col-tab-6 col-sm-6 col-md-4 col-lg-4">
    <div class="recent-post">
        <div class="box">
            <div class="post">
                <div class="head">
                    <div class="row">
                        <div class="user-photo col-lg-3 col-xs-3">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/user/user-profile', 'user' => $model['user']['username']]); ?>">

                                <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($model['user']['image']) ? Tools::thumb('/img/user/', $model['user']['image'], 100, 100) : '/img/user/default-avatar.png'), [
                                    'class' => 'img-responsive img-circle img-profile-thumb img-component',
                                    'height' => 50,
                                    'width' => 50
                                ]) ?>

                            </a>
                        </div>
                        <div class="user-name col-lg-9 col-xs-9">
                            <div class="full-name">

                                <?= Html::a($model['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $model['user']['username']])); ?>

                            </div>
                            <div class="created-at">

                                <small><?= Yii::$app->formatter->asDate($model['created_at'], 'medium'); ?></small>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-thumbnail">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12" id="user-<?= $model['id']; ?>-photos-review">

                            <?php
                            $img = Html::img(Yii::$app->urlManager->baseUrl . '/media/img/360x135.283no-image-available.jpg', ['class' => 'img-responsive img-component']);

                            if (!empty($model['userPostMains'][0]['image'])) {
                                $img = Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $model['userPostMains'][0]['image'], 360, 135.283));
                            } ?>

                            <?= Html::a($img, Yii::$app->urlManager->createUrl(['page/review', 'id' => $model['id']])); ?>

                        </div>
                    </div>
                </div>
                <div class="post-header">
                    <div class="row buttons">
                        <div class="col-sm-12 col-xs-12 col">
                            <ul class="list-inline mt-0 mb-10">
                                <li>

                                    <small><i class="fa fa-thumbs-up"></i><?= !empty($model['love_value']) ? ' ' . $model['love_value'] . ' Likes' : ' ' . 0 . ' Like' ?></small>

                                </li>
                                <li>

                                    <small><i class="fa fa-comments"></i><?= !empty($model['userPostComments']) ? ' ' . count($model['userPostComments']) . ' Comments' : ' ' . 0 . ' Comment' ?></small>

                                </li>
                                <li>

                                    <small><?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['id' => 'share-feature', 'class' => 'share-feature-' . $model['id'] . '-trigger']); ?></small>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col">
                            <h5 class="font-alt m-0 business-name">

                                <?= Html::a($model['business']['name'], Yii::$app->urlManager->createUrl(['page/detail', 'id' => $model['business']['id']])); ?>

                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-tab-6 col-sm-5 col-xs-5 col">
                            <div class="widget star-rating">

                                <?php
                                $ratingValue = 0;

                                foreach ($model['userVotes'] as $dataUserVotes) {
                                    $ratingValue += !empty($dataUserVotes['vote_value']) && !empty($model['userVotes']) ? ($dataUserVotes['vote_value'] / count($model['userVotes'])) : 0;
                                }

                                echo StarRating::widget([
                                    'id' => 'rating-' . $model['id'],
                                    'name' => 'rating_' . $model['id'],
                                    'value' => !empty($ratingValue) ? $ratingValue : 0,
                                    'pluginOptions' => [
                                        'displayOnly' => true,
                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                        'captionElement' => '.rating-' . strtolower($model['id']),
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
                            <div class="rating-<?= strtolower($model['id']) ?>"></div>
                        </div>
                    </div>
                </div>
                <div class="post-entry">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="review-description"><?= StringHelper::truncate($model['text'], 85, '. . .'); ?></div>

                            <?= (!empty($model['text']) ? '<br>' : '') . Html::a('<span class="text-red"> View detail <i class="fa fa-angle-double-right"></i></span>', ['page/review', 'id' => $model['id']]) ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
frontend\components\RatingColor::widget();

$jscript = '
    $(".user-post-main-id").each(function() {
        var thisObj = $(this);

        ratingColor($(".rating"), "span");

        $(".share-feature-" + thisObj.val() + "-trigger").on("click", function() {

            var businessName = thisObj.parent().find(".business-name").text().trim();
            var rating = thisObj.parent().find(".rating").text().trim();
            var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + thisObj.val();
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
    });
';

$this->registerJs($jscript); ?>