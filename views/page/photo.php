<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Inflector;
use common\components\Helper;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $modelUserPostMain core\models\UserPostMain */

common\assets\OwlCarouselAsset::register($this);

$this->title = Yii::t('app', 'Photo') . ' ' . $modelUserPostMain['business']['name'];

$ogUrl = Yii::$app->urlManager->createAbsoluteUrl([
    'page/photo',
    'id' => $modelUserPostMain['id'],
    'uniqueName' => $modelUserPostMain['business']['unique_name'],
]);

$ogTitle = !empty($modelUserPostMain['business']['name']) ? 'Foto untuk ' . $modelUserPostMain['business']['name'] : 'Foto di Asikmakan';
$ogDescription = !empty($modelUserPostMain['text']) ? $modelUserPostMain['text'] : $this->title;
$ogImage = Yii::$app->params['endPointLoadImage'] . 'user-post?image=&w=490&h=276';

if (!empty($modelUserPostMain['image'])) {
    
    $ogImage = Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $modelUserPostMain['image'];
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
]); ?>

<div class="main">

    <section class="module-extra-small bg-main">
        <div class="container detail photo">

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
                                    
                                        $img = !empty($modelUserPostMain['user']['image']) ? $modelUserPostMain['user']['image'] . '&w=200&h=200' : 'default-avatar.png'; ?>

                                        <div class="photo-container">
    
                                            <?= Html::hiddenInput('user_post_main_id', $modelUserPostMain['id'], ['class' => 'user-post-main-id']) ?>
    
                                            <div class="row mb-10">
                                                <div class="col-md-4 col-sm-5 col-tab-7 col-xs-9">
                                                
                                                    <div class="widget">
                                                        <div class="widget-posts-image">
                                                        
                    									   <?= Html::a(Html::img(Yii::$app->params['endPointLoadImage'] . 'user?image=' . $img, [
                    									       'class' => 'img-responsive img-circle img-profile-thumb img-component'
                    									   ]), ['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) ?>
                    									   
                                                        </div>
                                
                                                        <div class="widget-posts-body">
                                                            <?= Html::a($modelUserPostMain['user']['full_name'], ['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) ?>
                                                            <br>
                                                            <small><?= Helper::asRelativeTime($modelUserPostMain['created_at']) ?></small>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
    
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="photo-review mt-10 mb-10">
                                                        <div class="row">
                                                            <div class="col-sm-offset-1 col-sm-10">
                                                            	<div class="owl-carousel owl-theme">
                                                                	<?= Html::img(null, ['class' => 'owl-lazy', 'data-src' => Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $modelUserPostMain['image']]) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <p class="review-description">
                                                        <?= $modelUserPostMain['text']; ?>
                                                    </p>
    												
    												<?php                   
                                					$loveCount = !empty($modelUserPostMain['love_value']) ? $modelUserPostMain['love_value'] : 0;
                                					$commentCount = !empty($modelUserPostMain['userPostComments']) ? count($modelUserPostMain['userPostComments']) : 0; 
                                					
                                					$loveSpanCount = '<span class="total-likes-photo">' . $loveCount . '</span>';
                                					$commentSpanCount = '<span class="total-comments-photo">' . $commentCount . '</span>';
                                					
                                					$selected = !empty($modelUserPostMain['userPostLoves'][0]) ? 'selected' : ''; ?>
    												
                                                    <div class="row visible-xs">
                                                        <div class="col-xs-3">
                                                            <ul class="list-inline mt-0 mb-0">
                                                                <li>    
                                                                    <small><?= '<i class="fa fa-thumbs-up"></i> <span class="total-likes-photo">' . $loveCount . '</span>' ?></small>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-xs-9 text-right">
                                                            <ul class="list-inline mt-0 mb-0">
                                                                <li>
                                                                    <small><?= $commentSpanCount . ' Comment' ?></small>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
    
                                                    <div class="row">
                                                        <div class="col-sm-7 col-tab-7 col-xs-12">
                                                            <ul class="list-inline list-review mt-0 mb-0">
                                                                <li>
                                                                
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> ' . $loveSpanCount . ' Like', ['action/submit-likes'], ['class' => 'btn btn-default btn-small btn-round-4 likes-photo-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', ['action/submit-likes'], ['class' => 'btn btn-default btn-small btn-round-4 likes-photo-trigger ' . $selected . ' visible-xs']); ?>
                                                                    
                                                                </li>
                                                                <li>
                                                                
                                                                    <?= Html::a('<i class="fa fa-comments"></i> ' . $commentSpanCount . ' Comment', '', ['class' => 'btn btn-default btn-small btn-round-4 comments-photo-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                                    <?= Html::a('<i class="fa fa-comments"></i> Comment', '', ['class' => 'btn btn-default btn-small btn-round-4 comments-photo-trigger visible-xs']); ?>
                                                                    
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
                                                        <div class="user-comment-review" id="comments-photo-container">
                                                            <div class="col-sm-12">
                                                                <div class="input-group mt-10 mb-10">
                                                                    <span class="input-group-addon"><i class="fa fa-comment"></i></span>
                                                                    <?= Html::textInput('comment_input', null, ['id' => 'input-comments-photo', 'class' => 'form-control', 'placeholder' => Yii::t('app', 'Write a Comment')]); ?>
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
                                                                                            $img = !empty($dataUserPostComment['user']['image']) ? $dataUserPostComment['user']['image'] . '&w=200&h=200' : 'default-avatar.png';
                                                                                            
                                                                                            echo Html::a(Html::img(Yii::$app->params['endPointLoadImage'] . 'user?image=' . $img, [
                                                                                                'class' => 'img-responsive img-circle img-comment-thumb img-component'
                                                                                            ]), ['user/user-profile', 'user' => $dataUserPostComment['user']['username']]); ?>

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
GrowlCustom::widget();
frontend\components\FacebookShare::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    var photoId = $(".user-post-main-id");

    $(".likes-photo-trigger").on("click", function() {

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": photoId.val()
            },
            url: $(this).attr("href"),
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt($(".total-likes-photo").html());

                    if (response.is_active) {

                        $(".likes-photo-trigger").addClass("selected");
                        $(".total-likes-photo").html((loveValue + 1));
                    } else {

                        $(".likes-photo-trigger").removeClass("selected");
                        $(".total-likes-photo").html((loveValue - 1));
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

    $(".comments-photo-trigger").on("click", function() {

        $("#comments-photo-container").slideToggle();
        $("#input-comments-photo").trigger("focus");

        return false;
    });

    $("#input-comments-photo").on("keypress", function(event) {

        if (event.which == 13 && $(this).val().trim()) {

            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "user_post_main_id": photoId.val(),
                    "text": $(this).val(),
                },
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {

                    $(".comment-section").siblings(".overlay").show();
                    $(".comment-section").siblings(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {

                        $("#input-comments-photo").val("");

                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": response.user_post_main_id
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                            success: function(response) {

                                $(".comment-section").html(response);

                                $(".total-comments-photo").html(commentCount);
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

    $(".share-review-trigger").on("click", function() {

        facebookShare({
            ogUrl: "' . $ogUrl . '",
            ogTitle: "' . $ogTitle . '",
            ogDescription: "' . addslashes($ogDescription) . '",
            ogImage: "' . $ogImage . '",
            type: "Foto"
        });

        return false;
    });

    $(".photo-review").find(".owl-carousel").owlCarousel({
        lazyLoad: true,
        items: 1,
        mouseDrag: false,
        touchDrag: false
    });
';

$this->registerJs($jscript); ?>