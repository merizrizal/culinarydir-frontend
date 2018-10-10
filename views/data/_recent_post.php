<?php
use yii\helpers\Html;
use yii\helpers\StringHelper;
use kartik\rating\StarRating;
use sycomponent\Tools;
use common\components\Helper;

/* @var $this yii\web\View */
/* @var $model core\models\UserPostMain */ ?>

<div class="col-xs-12 col-tab-6 col-sm-6 col-md-4 col-lg-4">
    <div class="recent-post">
        <div class="box">
            <div class="post">
                <div class="head">
                    <div class="row">
                        <div class="user-photo col-lg-3 col-xs-3">
                        
                        	<?php 
                        	$img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png'; 
                        	
                        	if (!empty($model['user']['image'])) {
                        	    
                        	    $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', $model['user']['image'], 100, 100);
                        	}
                        	
                        	echo Html::a(Html::img($img, [
                        	    'class' => 'img-responsive img-circle img-profile-thumb img-component',
                        	    'height' => 50,
                        	    'width' => 50
                        	]), ['/user/user-profile', 'user' => $model['user']['username']]) ?>
                        	
                        </div>
                        <div class="user-name col-lg-9 col-xs-9">
                            <div class="full-name">

                                <?= Html::a($model['user']['full_name'], ['user/user-profile', 'user' => $model['user']['username']]); ?>

                            </div>
                            <div class="post-date">

                                <small><?= Helper::asRelativeTime($model['updated_at'], 'medium'); ?></small>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-thumbnail">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">

                            <?php
                            $img = Yii::$app->urlManager->baseUrl . '/media/img/360x135.283no-image-available.jpg';

                            if (!empty($model['userPostMains'][0]['image'])) {
                                
                                $img = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $model['userPostMains'][0]['image'], 360, 135);
                            }

                            echo Html::a(Html::img($img, ['class' => 'img-responsive img-component']), ['page/review', 'id' => $model['id']]); ?>

                        </div>
                    </div>
                </div>
                
                <?php 
                $loveCount = !empty($model['love_value']) ? $model['love_value'] : 0;
                $commentCount = !empty($model['userPostComments']) ? count($model['userPostComments']) : 0; ?>
                
                <div class="post-header">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col">
                            <ul class="list-inline mt-0 mb-10">
                                <li>

                                    <small><i class="fa fa-thumbs-up"></i> <?= Yii::t('app', '{value, plural, =0{# Like} =1{# Like} other{# Likes}}', ['value' => $loveCount ]) ?></small>

                                </li>
                                <li>

                                    <small><i class="fa fa-comments"></i> <?= Yii::t('app', '{value, plural, =0{# Comment} =1{# Comment} other{# Comments}}', ['value' => $commentCount]) ?></small>

                                </li>
                                <li>

                                    <small><?= Html::a('<i class="fa fa-share-alt"></i> Share', '', ['id' => 'share-feature', 'class' => 'share-feature-' . $model['id'] . '-trigger']); ?></small>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col">
                            <h5 class="font-alt m-0">

                                <?= Html::a($model['business']['name'], ['page/detail', 'id' => $model['business']['id']]); ?>

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
                                    'value' => $ratingValue,
                                    'pluginOptions' => [
                                        'displayOnly' => true,
                                        'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                        'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                        'showCaption' => false,
                                    ]
                                ]); ?>

                            </div>
                        </div>
                        <div class="col-lg-8 col-tab-6 col-sm-7 col-xs-7 col pb-10">
                            <div class="rating rating-<?= $model['id']; ?>">
                                <h4 class="mt-0 mb-0"><span class="label label-success"><?= number_format($ratingValue, 1) ?></span></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-entry">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">

                            <?php
                            $textReview = !empty($model['text']) ? StringHelper::truncate($model['text'], 85, '. . .') . '<br>' : '';

                            $textReview .= Html::a('<span class="text-red"> ' . Yii::t('app', 'View Details') . ' <i class="fa fa-angle-double-right"></i></span>', ['page/review', 'id' => $model['id']]);

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

        var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review', 'id' => $model['id']]) . '";
        var title = "Rating " + $(".rating-' . $model['id'] . '").text().trim() + " untuk " + "' . $model['business']['name'] . '";
        var description = "' . $model['text'] . '";
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
';

$this->registerJs($jscript); ?>