<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\View;
use kartik\rating\StarRating;
use sycomponent\Tools;
use common\components\Helper;
use frontend\components\AddressType;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $modelUserPostMain core\models\UserPostMain */
/* @var $dataUserVoteReview array */

$this->title = 'Review ' . $modelUserPostMain['business']['name'];

$ogUrl = Yii::$app->urlManager->createAbsoluteUrl([
    'page/review',
    'id' => $modelUserPostMain['id'],
    'uniqueName' => $modelUserPostMain['business']['unique_name'],
]);

$ogOverallValue = number_format($dataUserVoteReview['overallValue'], 1);
$ogTitle = !empty($modelUserPostMain['business']['name']) && !empty($dataUserVoteReview['overallValue']) ? 'Rating ' . $ogOverallValue . ' untuk ' . $modelUserPostMain['business']['name'] : 'Review di Asikmakan';
$ogPerson = $modelUserPostMain['user']['full_name'];

$ogDescription = !empty($modelUserPostMain['text']) ? $modelUserPostMain['text'] : $this->title;
$ogDescription = StringHelper::truncate(preg_replace('/[\r\n]+/','' , $ogDescription), 300);

$ogImage = Yii::$app->urlManager->getHostInfo() . Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 490, 276);

$ogProductCategory = '';

if (!empty($modelUserPostMain['userPostMains'][0]['image'])) {
    
    $ogImage = Yii::$app->urlManager->getHostInfo() . Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $modelUserPostMain['userPostMains'][0]['image'];
}

foreach ($modelBusiness['businessProductCategories'] as $dataBusinessProductCategory) {
    
    if ($dataBusinessProductCategory['productCategory']['is_active']) {
        
        $ogProductCategory .= $dataBusinessProductCategory['productCategory']['name'] . ',';
    }
}

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => $ogDescription
]);

$this->registerMetaTag([
    'property' => 'og:url',
    'content' => $ogUrl
]);

$this->registerMetaTag([
    'property' => 'og:type',
    'content' => 'website'
]);

$this->registerMetaTag([
    'property' => 'og:title',
    'content' => $ogTitle
]);

$this->registerMetaTag([
    'property' => 'og:description',
    'content' => $ogDescription
]);

$this->registerMetaTag([
    'property' => 'og:image',
    'content' => $ogImage
]);

kartik\popover\PopoverXAsset::register($this); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail review">

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <?= Html::a('<i class="fa fa-angle-double-left"></i> ' . Yii::t('app', 'Back to Place Detail'), [
                        'page/detail', 
                        'city' => Inflector::slug($modelUserPostMain['business']['businessLocation']['city']['name']), 
                        'uniqueName' => $modelUserPostMain['business']['unique_name']
                    ], ['class' => 'btn btn-standard p-0']); ?>

                </div>
            </div>

            <div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="box bg-white">
                                <div class="box-content">

                                    <?php
                                    if (!empty($modelUserPostMain)):
                                        
                                        $img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';
										
										if (!empty($modelUserPostMain['user']['image'])) {
										    
										    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', $modelUserPostMain['user']['image'], 200, 200);
										}
										
										$overallValue = !empty($dataUserVoteReview['overallValue']) ? $dataUserVoteReview['overallValue'] : 0; ?>

                                        <div class="review-container">

                                            <?= Html::hiddenInput('user_post_main_id', $modelUserPostMain['id'], ['class' => 'user-post-main-id']) ?>

                                            <div class="row mb-10">
                                                <div class="col-md-4 col-sm-5 col-tab-7 col-xs-12">
                                                    <div class="widget">
                                                        <div class="widget-posts-image">
        													
        												    <?= Html::a(Html::img($img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']), ['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) ?>
        
                                                        </div>
        
                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], ['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) ?>
                                                            <br>
                                                            <small><?= Helper::asRelativeTime($modelUserPostMain['created_at']) ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-tab-5 visible-lg visible-md visible-sm visible-tab">
													<div class="rating">
                                                    	<h3 class="mt-0 mb-0">
                                                    		<?= Html::a(number_format($overallValue, 1), '#', ['id' => 'user-rating-popover', 'class' => 'label label-success']); ?>
                                                        </h3>
                                                    </div>
                                                    <div id="user-container-popover" class="popover popover-x popover-default popover-rating">
                                                        <div class="arrow"></div>
                                                        <div class="popover-body popover-content">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-xs-12">
                                                                    <div class="widget star-rating">
                                                                        <ul class="icon-list">

                                                                            <?php
                                                                            $ratingComponent = [];
                                                                            
                                                                            foreach ($modelUserPostMain['userVotes'] as $dataUserVote) {
                                                                                
                                                                                if (!empty($dataUserVote['ratingComponent'])) {
                                                                                    
                                                                                    $ratingComponent[$dataUserVote['rating_component_id']] = $dataUserVote;
                                                                                }
                                                                            }
                                                                            
                                                                            ksort($ratingComponent);
                                                                            
                                                                            if (!empty($ratingComponent)):

                                                                                foreach ($ratingComponent as $dataUserVote): ?>

                                                                                    <li>
                                                                                        <div class="row">
                                                                                            <div class="col-sm-5 col-xs-5">

                                                                                                <?= StarRating::widget([
                                                                                                    'id' => 'user-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                                                    'name' => 'user-' . strtolower($dataUserVote['ratingComponent']['name']) . '-rating',
                                                                                                    'value' => $dataUserVote['vote_value'],
                                                                                                    'pluginOptions' => [
                                                                                                        'displayOnly' => true,
                                                                                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                                                        'showCaption' => false,
                                                                                                    ]
                                                                                                ]); ?>

                                                                                            </div>

                                                                                            <div class="col-sm-7 col-xs-7">

                                                                                                <?= $dataUserVote['vote_value'] . ' &nbsp;&nbsp;&nbsp;' . Yii::t('app', $dataUserVote['ratingComponent']['name']); ?>

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
                                                <div class="col-xs-12 visible-xs">
                                                	<ul class="list-inline mt-0 mb-0">
                                                        <li>
                                                            <div class="widget star-rating">
                                
                                                                <?= StarRating::widget([
                                                                    'id' => 'rating-' . $modelUserPostMain['id'],
                                                                    'name' => 'rating_' . $modelUserPostMain['id'],
                                                                    'value' => $overallValue,
                                                                    'pluginOptions' => [
                                                                        'displayOnly' => true,
                                                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                        'showCaption' => false,
                                                                    ]
                                                                ]); ?>
                                
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="rating rating-<?= $modelUserPostMain['id']; ?>">
                                                                <h4 class="mt-0 mb-0">
                                                                	<?= Html::a(number_format($overallValue, 1), '#', ['id' => 'user-rating-popover', 'class' => 'label label-success']); ?>
                                                                </h4>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <p class="review-description">
                                                        <?= $modelUserPostMain['text']; ?>
                                                    </p>
                                             	</div>
                                          	</div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <ul class="works-grid works-grid-gut works-grid-5">

                                                        <?php
                                                        if (!empty($modelUserPostMain['userPostMains'])):

                                                            foreach ($modelUserPostMain['userPostMains'] as $modelUserPostMainChild): ?>

                                                                <li class="work-item gallery-photo-review">
                                                                    <div class="gallery-item post-gallery">
                                                                        <div class="gallery-image">
                                                                            <div class="work-image">
                                                                                <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $modelUserPostMainChild['image'], 200, 200), ['class' => 'img-component']); ?>
                                                                            </div>
                                                                            <div class="work-caption">
                                                                                <div class="work-descr">
                                                                                	<a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $modelUserPostMainChild['image']; ?>"><i class="fa fa-search"></i></a>
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
                        					$loveCount = !empty($modelUserPostMain['love_value']) ? $modelUserPostMain['love_value'] : 0;
                        					$commentCount = !empty($modelUserPostMain['userPostComments']) ? count($modelUserPostMain['userPostComments']) : 0;
                        					$photoCount = !empty($modelUserPostMain['userPostMains']) ? count($modelUserPostMain['userPostMains']) : 0;
                        					
                        					$loveSpanCount = '<span class="total-likes-review">' . $loveCount . '</span>'; 
                        					$commentSpanCount = '<span class="total-comments-review">' . $commentCount . '</span>';
                        					$photoSpanCount = '<span class="total-photos-review">' . $photoCount . '</span>';
                        					
                        					$selected = !empty($modelUserPostMain['userPostLoves'][0]) ? 'selected' : ''; ?>
                                					
                                            <div class="row visible-xs">
                                                <div class="col-xs-3">
                                                    <ul class="list-inline mt-0 mb-0">
                                                        <li>
                                                            <small><?= '<i class="fa fa-thumbs-up"></i> <span class="total-likes-review">' . $loveCount . '</span>' ?></small>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <ul class="list-inline mt-0 mb-0">
                                                        <li>
                                                            <small><?= $commentSpanCount . ' Comment' ?></small>
                                                        </li>
                                                        <li>
                                                            <small><?= $photoSpanCount . ' Photo' ?></small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                
                                            <div class="row">
                                                <div class="col-sm-7 col-tab-7 col-xs-12">
                                                    <ul class="list-inline list-review mt-0 mb-0">
                                                        <li>
                                                            <?= Html::a('<i class="fa fa-thumbs-up"></i> ' . $loveSpanCount . ' Like', ['action/submit-likes'] , ['class' => 'btn btn-default btn-small btn-round-4 likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                                            <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', ['action/submit-likes'], ['class' => 'btn btn-default btn-small btn-round-4 likes-review-trigger ' . $selected . ' visible-xs']); ?>
                                                        </li>
                                                        <li>
                                                            <?= Html::a('<i class="fa fa-comments"></i> ' . $commentSpanCount . ' Comment', '', ['class' => 'btn btn-default btn-small btn-round-4 comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                            <?= Html::a('<i class="fa fa-comments"></i> Comment', '', ['class' => 'btn btn-default btn-small btn-round-4 comments-review-trigger visible-xs']); ?>
                                                        </li>
                                                        <li class="visible-xs-inline-block">
                                                            <?= Html::a('<i class="fa fa-share-alt"></i> ', '', ['class' => 'btn btn-default btn-small btn-round-4 share-review-trigger']); ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-sm-5 col-tab-5 text-right visible-lg visible-md visible-sm visible-tab">
                                                    <ul class="list-inline list-review mt-0 mb-0">
                                                        <li>
                                                            <?= Html::a('<i class="fa fa-share-alt"></i> Share', '', ['class' => 'btn btn-default btn-small btn-round-4 share-review-trigger']); ?>
                                                        </li>
                                                    </ul>
                                            	</div>
                                            </div>

                                            <hr class="divider-w mt-10">

                                            <div class="row">
                                                <div class="user-comment-review" id="comments-review-container">
                                                    <div class="col-sm-12">
                                                        <div class="input-group mt-10 mb-10">
                                                            <span class="input-group-addon"><i class="fa fa-comment"></i></span>
                                                            <?= Html::textInput('comment_input', null, ['id' => 'input-comments-review', 'class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Comment')]); ?>
                                                        </div>

                                                        <div class="overlay" style="display: none;"></div>
                                                        <div class="loading-img" style="display: none;"></div>
                                                        
                                                        <div class="comment-section">

                                                            <?php
                                                            foreach ($modelUserPostMain['userPostComments'] as $dataUserPostComment): ?>

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
                                                                                    
                                                                                    echo Html::a(Html::img($img, ['class' => 'img-responsive img-circle img-comment-thumb img-component']), ['user/user-profile', 'user' => $dataUserPostComment['user']['username']]); ?>

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
                                                            endforeach; ?>

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

GrowlCustom::widget();
frontend\components\RatingColor::widget();
frontend\components\Readmore::widget();
frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var reviewId = $(".user-post-main-id");

    ratingColor($(".rating"), "a");

    readmoreText({
        element: $(".review-description"),
        minChars: 500,
        ellipsesText: " . . . ",
        moreText: "See more",
        lessText: "See less",
    });

    $("#user-rating-popover").popoverButton({
        trigger: "hover",
        placement: "right right-top",
        target: "#user-container-popover"
    });

    $(".likes-review-trigger").on("click", function() {

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": reviewId.val()
            },
            url: $(this).attr("href"),
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt($(".total-likes-review").html());

                    if (response.is_active) {

                        $(".likes-review-trigger").addClass("selected");
                        $(".total-likes-review").html((loveValue + 1));
                    } else {

                        $(".likes-review-trigger").removeClass("selected");
                        $(".total-likes-review").html((loveValue - 1));
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

    $(".comments-review-trigger").on("click", function() {

        $("#comments-review-container").slideToggle();
        $("#input-comments-review").trigger("focus");

        return false;
    });

    $("#input-comments-review").on("keypress", function(event) {

        if (event.which == 13 && $(this).val().trim()) {

            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "user_post_main_id": reviewId.val(),
                    "text": $(this).val(),
                },
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {

                    $(".comment-section").siblings(".overlay").show();
                    $(".comment-section").siblings(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {

                        $("#input-comments-review").val("");

                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": response.user_post_main_id
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                            success: function(response) {

                                $(".comment-section").html(response);

                                $(".total-comments-review").html(commentCount);
                            },
                            error: function(xhr, ajaxOptions, thrownError) {

                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                            }
                        });
                    } else {

                        messageResponse(response.icon, response.title, response.message, response.type);
                    }

                    $(".comment-section").siblings(".overlay").hide();
                    $(".comment-section").siblings(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                    $(".comment-section").siblings(".overlay").hide();
                    $(".comment-section").siblings(".loading-img").hide();
                }
            });
        }
    });

    $(".post-gallery").magnificPopup({

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

    $(".share-review-trigger").on("click", function() {

        facebookShare({
            ogUrl: "' . $ogUrl . '",
            ogTitle: "' . $ogTitle . '",
            ogDescription: "' . addslashes($ogDescription) . '",
            ogImage: "' . $ogImage . '",
            type: "Review"
        });

        return false;
    });
';

$this->registerJs($jscript); 

$this->on(View::EVENT_END_BODY, function() use ($modelBusiness, $ogImage, $ogProductCategory, $ogOverallValue, $ogTitle, $ogPerson, $ogDescription) {
    
    echo '
        <script type="application/ld+json">
        {
            "@context": "http://schema.org/",
            "@type": "Review",
            "itemReviewed": {
                "@type": "Restaurant",
                "name": "' . $modelBusiness['name'] . '",
                "image": "' . $ogImage . '",
                "menu": "' . Yii::$app->urlManager->createAbsoluteUrl(['page/menu', 'uniqueName' => $modelBusiness['unique_name']]) . '",
                "servesCuisine": "' . trim($ogProductCategory, ',') . '",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "' . AddressType::widget([
                        'businessLocation' => $modelBusiness['businessLocation'],
                        'showDetail' => true
                    ]). '",
                    "AddressLocality": "' . $modelBusiness['businessLocation']['city']['name'] . '"
                }
            },
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "' . number_format($ogOverallValue, 1) . '"
            },
            "name": "' . $ogTitle . '",
            "author": {
                "@type": "Person",
                "name": "' . $ogPerson . '"
            },
            "reviewBody": "' . $ogDescription . '"
        }
        </script>
    ';
}); ?>