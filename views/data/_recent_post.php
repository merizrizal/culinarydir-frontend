<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use kartik\rating\StarRating;
use sycomponent\Tools;
use common\components\Helper;

/* @var $this yii\web\View */
/* @var $model core\models\UserPostMain */

$urlReviewDetail = [
    'page/review',
    'id' => $model['id'],
    'uniqueName' => $model['business']['unique_name'],
]; ?>

<div class="col-lg-4 col-md-4 col-sm-6 col-tab-6 col-xs-12">
    <div class="recent-post">
        <div class="box">
            <div class="post">
                <div class="head">
                    <div class="row">
                        <div class="col-xs-12">
                        	<div class="widget">
                        		<div class="widget-posts-image">
                        		
                        			<?php
                                	$img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png'; 
                                	
                                	if (!empty($model['user']['image'])) {
                                	    
                                	    $img = Yii::$app->params['endPointLoadImage'] . 'user?image=' . $model['user']['image'] . '&w=100&h=100';
                                	}
                                	
                                	echo Html::a(Html::img($img, ['class' => 'img-responsive img-circle img-profile-thumb img-component']), ['/user/user-profile', 'user' => $model['user']['username']]) ?>
                                	
                        		</div>
                        		
                        		<div class="widget-posts-body">
                                    <?= Html::a($model['user']['full_name'], ['user/user-profile', 'user' => $model['user']['username']]); ?>
                                    <br>
                                    <small><?= Helper::asRelativeTime($model['created_at'], 'medium'); ?></small>
                        		</div>
                        	</div>
                    	</div>
                    </div>
                </div>
                <div class="post-thumbnail">
                    <div class="row">
                        <div class="col-xs-12">

                            <?php
                            $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/', 'image-no-available.jpg', 478, 165, false, false);

                            if (!empty($model['userPostMains'][0]['image'])) {
                                
                                $img = Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $model['userPostMains'][0]['image'] . '&w=478&h=165';
                            }

                            echo Html::a(Html::img($img, ['class' => 'img-responsive img-component']), $urlReviewDetail); ?>

                        </div>
                    </div>
                </div>
                
                <?php 
                $loveCount = !empty($model['love_value']) ? $model['love_value'] : 0;
                $commentCount = !empty($model['userPostComments']) ? count($model['userPostComments']) : 0;
                
                $selected = !empty($model['userPostLoves'][0]) ? 'selected' : ''; ?>
                
                <div class="post-header">
                    <div class="row">
                        <div class="col-xs-12 col">
                            <ul class="list-inline mt-0 mb-10">
                                <li>
                                
                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> <span class="total-likes-review">' . $loveCount . '</span> Like', ['action/submit-likes'], [
                                        'class' => 'btn btn-default btn-small btn-round-4 user-likes-review-' . $model['id'] . '-trigger ' . $selected,
                                    ]) ?>
                                    
                                </li>
                                <li>
                                    <?= Html::a('<i class="fa fa-comments"></i> ' . $commentCount . ' Comment', $urlReviewDetail, ['class' => 'btn btn-default btn-small btn-round-4']) ?>
                                </li>
                                <li>
                                    <?= Html::a('<i class="fa fa-share-alt"></i> Share', '', ['class' => 'btn btn-default btn-small btn-round-4 share-feature-' . $model['id'] . '-trigger visible-lg visible-sm']); ?>
                                    <?= Html::a('<i class="fa fa-share-alt"></i> ', '', ['class' => 'btn btn-default btn-small btn-round-4 share-feature-' . $model['id'] . '-trigger visible-md visible-tab']); ?>
                                    <?= Html::a('<i class="fa fa-share-alt"></i> ', '', ['class' => 'btn btn-default btn-small btn-round-4 share-feature-' . $model['id'] . '-trigger visible-xs']); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col">
                            <h4 class="m-0">
                            
                                <?= Html::a($model['business']['name'], [
                                    'page/detail', 
                                    'city' => Inflector::slug($model['business']['businessLocation']['city']['name']), 
                                    'uniqueName' => $model['business']['unique_name']
                                ]); ?>
                            
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-xs-12 col">
                        	<ul class="list-inline mt-0 mb-10">
                                <li>
                                    <div class="widget star-rating">
        
                                        <?php
                                        $ratingValue = 0;
        
                                        foreach ($model['userVotes'] as $dataUserVotes) {
                                            
                                            $ratingValue += !empty($dataUserVotes['vote_value']) && !empty($model['userVotes']) ? ($dataUserVotes['vote_value'] / count($model['userVotes'])) : 0;
                                        }
        
                                        echo StarRating::widget([
                                            'id' => 'rating-' . $model['id'],
                                            'name' => 'rating_' . $model['id'],
                                            'value' => $ratingValue,
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
                                    <div class="rating rating-<?= $model['id']; ?>">
                                        <h4 class="mt-0 mb-0"><span class="label label-success"><?= number_format($ratingValue, 1) ?></span></h4>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="post-entry">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">

                            <?php
                            $textReview = !empty($model['text']) ? StringHelper::truncate($model['text'], 80, '. . .') . '<br>' : '';
                            $textReview .= Html::a('<span class="text-red"> ' . Yii::t('app', 'View Details') . ' <i class="fa fa-angle-double-right"></i></span>', $urlReviewDetail);

                            echo $textReview; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
    ratingColor($(".rating-' . $model['id'] . '"), "span");

    $(".share-feature-' . $model['id'] . '-trigger").on("click", function() {

        var url = "' . Yii::$app->urlManager->createAbsoluteUrl($urlReviewDetail) . '";
        var title = "Rating " + $(".rating-' . $model['id'] . '").text().trim() + " untuk " + "' . $model['business']['name'] . '";
        var description = "' . addslashes($model['text']) . '";
        var image = window.location.protocol + "//" + window.location.hostname + "'. $img . '";

        facebookShare({
            ogUrl: url,
            ogTitle: title,
            ogDescription: description,
            ogImage: image,
            type: "Review"
        });

        return false;
    });

    $(".user-likes-review-' . $model['id'] . '-trigger").on("click", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": "' . $model['id'] . '"
            },
            url: thisObj.attr("href"),
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt(thisObj.find(".total-likes-review").html());

                    if (response.is_active) {
                        
                        thisObj.addClass("selected");
                        thisObj.find(".total-likes-review").html(loveValue + 1);
                    } else {
                        
                        thisObj.removeClass("selected");
                        thisObj.find(".total-likes-review").html(loveValue - 1);
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
';

$this->registerJs($jscript); ?>