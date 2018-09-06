<?php
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\BaseStringHelper;
use sycomponent\Tools;
use kartik\rating\StarRating;
use frontend\components\AppComponent;

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]); ?>

<div class="main">

    <section class="module-small bg-dark visible-lg visible-md visible-sm" id="home" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
        <div class="titan-caption">
            <div class="caption-content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                            <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">

                            <!--<div class="overlay-coming-soon">

                                <div class="titan-caption">
                                    <div class="caption-content">
                                        <div class="font-alt mb-30 titan-title-size-3">Asikmakan</div>
                                        <div class="font-alt mb-30 titan-title-size-4">Coming Soon</div>
                                        <div class="font-alt">Website masih dalam tahap development</div>
                                        <div class="font-alt mt-10">
                                            <a class="section-scroll text-center text-white" href="#footer">
                                                <i class="fa fa-angle-double-down fa-4x animate-bounce"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>-->

                            <?php
                            $appComponent = new AppComponent();
                            echo $appComponent->search(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="module-small visible-tab" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
                    <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= $appComponent->search([
                        'id' => 'tab-search'
                    ]); ?>

                </div>
            </div>
        </div>
    </section>

    <section class="module-small visible-xs" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
                    <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= $appComponent->search([
                        'id' => 'xs-search'
                    ]); ?>

                </div>
            </div>
        </div>
    </section>

    <section class="module-extra-small in-result bg-main">
        <div class="container detail">
            <div class="view">
                <div class="row recent-post">
                <div class="font-alt mb-20 align-center visible-xs">Recent Activity</div>
                <div class="font-alt mb-20 align-center visible-tab">Recent Activity</div>
                <div class="font-alt mb-20 visible-lg visible-md visible-sm">Recent Activity</div>

                    <?php
                    foreach ($modelUserPostMain as $dataUserPostMain):

                        if ($dataUserPostMain['type'] === 'Review'): ?>

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
                                                    } ?>

                                                    <a href="<?= Yii::$app->urlManager->createUrl([$url, 'id' => $dataUserPostMain['id']]); ?>">

                                                        <?php
                                                        $img = Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', 'no-image-available.jpg', 360, 135.283));

                                                        if ($dataUserPostMain['type'] === 'Photo') {

                                                            if (!empty($dataUserPostMain['image']) && file_exists(Yii::getAlias('@uploads') . '/img/user_post/' . $dataUserPostMain['image'])) {
                                                                $img = Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMain['image'], 360, 135.283));
                                                            }

                                                        } else if ($dataUserPostMain['type'] === 'Review' && !empty($dataUserPostMain['userPostMains'])) {

                                                            if (!empty($dataUserPostMain['userPostMains'][0]['image']) && file_exists(Yii::getAlias('@uploads') . '/img/user_post/' . $dataUserPostMain['userPostMains'][0]['image'])) {
                                                                $img= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMain['userPostMains'][0]['image'], 360, 135.283));
                                                            }

                                                        }

                                                        echo $img; ?>

                                                    </a>
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

                                                            <small><?= Html::a('<i class="fa fa-share-alt"></i> Share', null, ['class' => 'share-feature']); ?></small>

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

                        <?php
                        endif; ?>

                    <?php
                    endforeach; ?>

                </div>
            </div>
        </div>
    </section>
</div>

<?= $appComponent->searchJsComponent(); ?>

<?php
$csscript = '
    .overlay-coming-soon {
        z-index: 1010;
        background: rgba(100, 100, 100, 0.7);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
';

$this->registerCss($csscript);

frontend\components\RatingColor::widget();

$jscript = '

    ratingColor($(".rating"), "span");

    $(".share-feature").on("click", function() {

        url = window.location.href;

        FB.ui({
            method: "share",
            href: url,
        }, function(response){

        });

        return false;
    });

    $(".review-container").find(".share-review-" + reviewId.val() + "-trigger").on("click", function() {

        var businessName = $(".business-name").val();
        var rating = $(".review-container").find(".rating").text().trim();

        var url = window.location.href;
        var title = "Rating " + rating + " untuk " + businessName;
        var description = $(".review-container").find(".review-description").text();
        var image = window.location.protocol + "//" + window.location.hostname + $(".review-container").find("#user-" + reviewId.val() + "-photos-review").eq(0).find(".work-image").children().attr("src");

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
';

$this->registerJs($jscript); ?>
