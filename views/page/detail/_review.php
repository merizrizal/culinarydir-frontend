<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\file\FileInput;
use kartik\rating\StarRating;
use sycomponent\Tools;
use common\components\Helper;

/* @var $this yii\web\View */

kartik\popover\PopoverXAsset::register($this);

$reviewTotal = !empty($modelUserPostMain) ? 1 : 0;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$imgUserProfile = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';

if (!empty(Yii::$app->user->getIdentity()->image)) {

    $imgUserProfile = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', Yii::$app->user->getIdentity()->image, 200, 200);
}

$layoutUser = '
    <div class="widget-posts-image">
        <a href="' . Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) . '">

            ' . Html::img($imgUserProfile, ['class' => 'img-responsive img-circle img-profile-thumb img-component']) . '

        </a>
    </div>

    <div class="widget-posts-body">
        ' . Html::a($modelUserPostMain['user']['full_name'], ['user/user-profile', 'user' => $modelUserPostMain['user']['username']]) . '
        <br>
        <small>' . Helper::asRelativeTime($modelUserPostMain['created_at']) . '</small>
    </div>
'; ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">

            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none"></div>

            <div class="box-title" id="title-write-review">
                <h4 class="mt-0 mb-0 inline-block"><?= !empty($modelUserPostMain) ? Yii::t('app', 'Your Review') : Yii::t('app', 'Write a Review') ?></h4>
                <span class="pull-right inline-block" id="close-review-container"><a class="text-main" href=""><i class="fa fa-close"></i> Cancel</a></span>
            </div>

            <div class="box-content">

                <div class="form-group <?= empty($modelUserPostMain) ? 'hidden' : '' ?>" id="edit-review-container">

                    <?= Html::hiddenInput('user_post_main_id', $modelUserPostMain['id'], ['class' => 'my-user-post-main-id']) ?>

                    <div class="row">
                        <div class="col-md-12">
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

                                    <h3 class="mt-0 mb-0">
                                        <div class="my-rating">

                                            <?= Html::a(number_format((float) !empty($dataUserVoteReview['overallValue']) ? $dataUserVoteReview['overallValue'] : 0, 1, '.', ''), null, ['id' => 'my-rating-popover', 'class' => 'label label-success']); ?>

                                        </div>
                                    </h3>

                                    <div id="my-popover-container" class="popover popover-x popover-default popover-rating">
                                        <div class="arrow"></div>
                                        <div class="popover-body popover-content">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="widget star-rating">
                                                        <ul class="icon-list">

                                                            <?php
                                                            if (!empty($modelRatingComponent)):

                                                                foreach ($modelRatingComponent as $dataRatingComponent):

                                                                    if (!empty($dataUserVoteReview['ratingComponentValue'])) {

                                                                        foreach ($dataUserVoteReview['ratingComponentValue'] as $ratingComponentId => $vote_value) {

                                                                            if ($dataRatingComponent['id'] == $ratingComponentId) {

                                                                                $valueRatingComponent = $vote_value;
                                                                            }
                                                                        }
                                                                    } ?>

                                                                        <li>
                                                                            <div class="row">
                                                                                <div class="col-sm-5 col-xs-5">

                                                                                    <?= StarRating::widget([
                                                                                        'id' => 'my-rating-' . $dataRatingComponent['id'],
                                                                                        'name' => 'my_rating_' . $dataRatingComponent['id'],
                                                                                        'value' => !empty($valueRatingComponent) ? $valueRatingComponent : null,
                                                                                        'pluginOptions' => [
                                                                                            'displayOnly' => true,
                                                                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                                            'captionElement' => '.rating-' . $dataRatingComponent['id'],
                                                                                            'starCaptions' => new JsExpression('function(val){return val == 1 ? "1 &nbsp;&nbsp;&nbsp;' . $dataRatingComponent['name'] . '" : val + " &nbsp;&nbsp;&nbsp;' . $dataRatingComponent['name'] . '";}'),
                                                                                            'starCaptionClasses' => new JsExpression('function(val){ return false;}'),
                                                                                        ]
                                                                                    ]); ?>

                                                                                </div>

                                                                                <div class="col-sm-7 col-xs-7">
                                                                                    <div class="rating-<?= $dataRatingComponent['id'] ?>"></div>
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
                                    <p class="my-review-description">

                                        <?= !empty($modelUserPostMain['text']) ? $modelUserPostMain['text'] : null ?>

                                    </p>

                                    <div class="row" id="my-photos-review-container">
                                        <div class="col-sm-12 col-xs-12">
                                            <ul class="works-grid works-grid-gut works-grid-5" id="review-uploaded-photo">

                                                <?php
                                                if (!empty($modelUserPostMain['userPostMains'])):

                                                    foreach ($modelUserPostMain['userPostMains'] as $modelUserPostMainChild): ?>

                                                        <li id="image-<?= $modelUserPostMainChild['id'] ?>" class="work-item gallery-photo-review">
                                                            <div class="gallery-item review-post-gallery">
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

                                    <div class="row visible-xs">
                                        <div class="col-xs-3">
                                            <ul class="list-inline mt-0 mb-0">
                                                <li>

                                                    <small><?= !empty($modelUserPostMain['love_value']) ? '<i class="fa fa-thumbs-up"></i> <span class="my-total-likes-review">' . $modelUserPostMain['love_value'] . '</span>' : '<i class="fa fa-thumbs-up"></i> <span class="my-total-likes-review">0</span>' ?></small>

                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <ul class="list-inline mt-0 mb-0">
                                                <li>

                                                    <small><?= !empty($modelUserPostMain['userPostComments']) ? '<span class="total-' . $modelUserPostMain['id'] . '-comments-review">' . count($modelUserPostMain['userPostComments']) . '</span> Comment' : '<span class="my-total-comments-review">0</span> Comment' ?></small>

                                                </li>
                                                <li>

                                                    <small><?= !empty($modelUserPostMain['userPostMains']) ? '<span class="my-total-photos-review">' . count($modelUserPostMain['userPostMains']) . '</span> Photo' : '<span class="my-total-photos-review">0</span> Photo' ?></small>

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

                                                    if (!empty($modelUserPostMain['userPostLoves'][0])) {
                                                        $selected = 'selected';
                                                    } 
                                                    
                                                    $commentCount = 0;
                                                    $photoCount = 0;
                                                    
                                                    if (!empty($modelUserPostMain)) {
                                                        $commentCount = count($modelUserPostMain['userPostComments']);
                                                        $photoCount = count($modelUserPostMain['userPostMains']);
                                                    } ?>
                                                
                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> <span class="my-total-likes-review">' . $modelUserPostMain['love_value'] . '</span>' . Yii::t('app', '{value, plural, =0{ Like} =1{ Like} other{ Likes}}', ['value' => !empty($modelUserPostMain['love_value']) ? $modelUserPostMain['love_value'] : 0]), null , ['class' => 'my-likes-review-trigger ' . $selected . ' visible-lg visible-md visible-sm visible-tab']); ?>
                                                    <?= Html::a('<i class="fa fa-thumbs-up"></i> Like', null, ['class' => 'my-likes-review-trigger ' . $selected . ' visible-xs']); ?>
            
                                                </li>
                                                <li>
            
                                                    <?= Html::a('<i class="fa fa-comments"></i> <span class="total-' . $modelUserPostMain['id'] . '-comments-review">' . $commentCount . '</span>' . Yii::t('app', '{value, plural, =0{ Comment} =1{ Comment} other{ Comments}}', ['value' => $commentCount]), null, ['class' => 'my-comments-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                    <?= Html::a('<i class="fa fa-comments"></i> Comment', null, ['class' => 'my-comments-review-trigger visible-xs']); ?>
            
                                                </li>
                                                <li>
            
                                                    <?= Html::a('<i class="fa fa-camera-retro"></i> <span class="my-total-photos-review">' . $photoCount . '</span>' . Yii::t('app', '{value, plural, =0{ Photo} =1{ Photo} other{ Photos}}', ['value' => $photoCount]), null, ['class' => 'my-photos-review-trigger visible-lg visible-md visible-sm visible-tab']); ?>
                                                    <?= Html::a('<i class="fa fa-camera-retro"></i> Photo', null, ['class' => 'my-photos-review-trigger visible-xs']); ?>
            
                                                </li>
                                                <li class="review-option-toggle visible-xs-inline-block">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-5 col-tab-5 text-right visible-lg visible-md visible-sm visible-tab">
                                            <ul class="list-inline list-review mt-0 mb-0">
                                                <li>

                                                    <?= Html::a('<i class="fa fa-edit"></i> Edit', null, ['class' => 'edit-my-review-trigger']); ?>

                                                </li>
                                                <li>

                                                    <?= Html::a('<i class="fa fa-trash"></i> Delete', ['user-action/delete-user-post', 'id' => $modelUserPostMain['id']], ['class' => 'delete-my-review-trigger']); ?>

                                                </li>
                                            </ul>
                                        </div>
                                        <div class="review-option col-xs-12">
                                            <ul class="list-inline list-review mt-0 mb-0">
                                                <li>

                                                    <?= Html::a('<i class="fa fa-edit"></i> Edit', null, ['class' => 'edit-my-review-trigger']); ?>

                                                </li>
                                                <li>

                                                    <?= Html::a('<i class="fa fa-trash"></i> Delete', ['user-action/delete-user-post', 'id' => $modelUserPostMain['id']], ['class' => 'delete-my-review-trigger']); ?>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <hr class="divider-w mt-10">

                                    <div class="row">
                                        <div class="user-comment-review" id="my-comments-review-container">
                                            <div class="col-sm-12">
                                                <div class="input-group mt-10 mb-10">
                                                    <span class="input-group-addon" id="basic-addon1"><i class="fa fa-comment"></i></span>

                                                    <?= Html::textInput('comment_input', null, ['id' => 'input-my-comments-review', 'class' => 'form-control', 'placeholder' => 'Tuliskan komentar']); ?>

                                                </div>

                                                <div class="overlay" style="display: none;"></div>
                                                <div class="loading-img" style="display: none"></div>
                                                <div class="my-comment-section">
                                                    <div class="my-comment-container">

                                                        <?php
                                                        $userReviewComment = [];

                                                        if (!empty($modelUserPostMain['userPostComments'])):

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
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostComment['user']['username']]) ?>">

                                                                                        <?php
                                                                                        $imgUserProfileComment = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';
    
                                                                                        if (!empty($dataUserPostComment['user']['image'])) {
    
                                                                                            $imgUserProfileComment = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200);
                                                                                        }
    
                                                                                        echo Html::img($imgUserProfileComment, ['class' => 'img-responsive img-circle img-comment-thumb img-component']); ?>

                                                                                    </a>
                                                                                </div>

                                                                                <div class="widget-comments-body">
                                                                                    <?= Html::a($dataUserPostComment['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostComment['user']['username']])); ?>&nbsp;&nbsp;&nbsp;
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
                        </div>
                    </div>
                </div>

                <div class="form-group has-feedback <?= !empty($modelUserPostMain) ? 'hidden' : '' ?>" id="write-review-trigger">
                    <i class="fa fa-pencil-alt form-control-feedback"></i>
                    <input type="text" class="form-control" placeholder="Bagikan pengalamanmu disini" />
                </div>

                <?php
                $form = ActiveForm::begin([
                    'id' => 'review-form',
                    'action' => ['action/submit-review'],
                    'enableClientValidation' => false,
                    'fieldConfig' => [
                        'template' => '{input}',
                    ]
                ]); ?>

                    <?= Html::hiddenInput('business_id', $modelBusiness['id'], ['id' => 'business_id']); ?>

                    <div class="row" id="write-review-container">
                        <div class="col-lg-4 col-md-5 col-sm-5 col-tab-6 col-xs-12 mb-20">
                            <div class="row">
                                <div class="col-sm-6 col-tab-6 col-xs-6">
                                    <span><strong>Overall Rating</strong></span>

                                    <?= Html::hiddenInput('temp_overall_rating', null, ['class' => 'temp-overall-rating']) ?>

                                    <?= StarRating::widget([
                                        'id' => 'overall-rating',
                                        'name' => 'overall_rating',
                                        'value' => !empty($dataUserVoteReview['overallValue']) ? $dataUserVoteReview['overallValue'] : null,
                                        'pluginOptions' => [
                                            'step' => 1,
                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                            'showClear' => false,
                                            'clearCaption' => '0',
                                            'captionElement' => '.rating-overall',
                                            'starCaptions' => new JsExpression('function(val){return val == 1 ? "1" : val;}'),
                                            'starCaptionClasses' => new JsExpression('function(val){ return false;}'),
                                            'hoverChangeCaption' => false,
                                        ]
                                    ]); ?>

                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <h3 class="rating-overall mt-0 mb-0"></h3>
                                </div>
                            </div>

                            <div class="row mt-20 mb-20">
                                <div class="col-sm-12">
                                    <span><strong>O R</strong></span>
                                </div>
                            </div>

                            <div class="row form-rating">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="widget star-rating">
                                        <ul class="icon-list">

                                            <?php
                                            if (!empty($modelRatingComponent)):

                                                foreach ($modelRatingComponent as $dataRatingComponent):

                                                    if (!empty($dataUserVoteReview['ratingComponentValue'])) {

                                                        foreach ($dataUserVoteReview['ratingComponentValue'] as $ratingComponentId => $vote_value) {

                                                            if ($dataRatingComponent['id'] == $ratingComponentId) {

                                                                $valueRatingComponent = $vote_value;
                                                            }
                                                        }
                                                    } ?>

                                                        <li>
                                                            <div class="row">
                                                                <div class="col-sm-6 col-tab-6 col-xs-6">

                                                                    <?= Html::hiddenInput('rating_component_id', $dataRatingComponent['id'], ['class' => 'rating-component-id']) ?>

                                                                    <?= Html::hiddenInput('temp_rating_' . $dataRatingComponent['id'], null, ['class' => 'temp-rating-' . $dataRatingComponent['id']]) ?>

                                                                    <?= $form->field($modelPost, '[review]rating[' . $dataRatingComponent['id'] . ']')->hiddenInput(['value' => !empty($valueRatingComponent) ? $valueRatingComponent : null,]) ?>

                                                                    <?= StarRating::widget([
                                                                        'id' => 'rating-' . $dataRatingComponent['id'],
                                                                        'name' => 'rating_' . $dataRatingComponent['id'],
                                                                        'value' => !empty($valueRatingComponent) ? $valueRatingComponent : null,
                                                                        'pluginOptions' => [
                                                                            'step' => 1,
                                                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                                                            'showClear' => false,
                                                                            'clearCaption' => $dataRatingComponent['name'],
                                                                            'captionElement' => '.rating-' . $dataRatingComponent['id'],
                                                                            'starCaptions' => new JsExpression('function(val){return val == 1 ? "1 &nbsp;&nbsp;&nbsp;' . $dataRatingComponent['name'] . '" : val + " &nbsp;&nbsp;&nbsp;' . $dataRatingComponent['name'] . '";}'),
                                                                            'starCaptionClasses' => new JsExpression('function(val){ return false;}'),
                                                                        ]
                                                                    ]); ?>

                                                                </div>

                                                                <div class="col-sm-6 col-xs-6 business-rating-components">
                                                                    <div class="rating-<?= $dataRatingComponent['id'] ?>"></div>
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

                        <div class="col-lg-8 col-md-7 col-sm-7 col-tab-6 col-xs-12">
                            <div class="form-group">

                                <?= $form->field($modelPost, '[review]text')->textarea([
                                    'class' => 'form-control',
                                    'placeholder' => 'Bagikan pengalamanmu disini',
                                    'rows' => 6,
                                ]); ?>

                            </div>

                            <div class="form-group hidden">

                                <?= Html::label('Uploaded Photos') ?>

                                <div class="row" id="form-photos-review-container">
                                    <div class="col-sm-12 col-xs-12">
                                        <ul class="works-grid works-grid-gut works-grid-5" id="form-review-uploaded-photo">

                                            <?php
                                            if (!empty($modelUserPostMain['userPostMains'])):

                                                foreach ($modelUserPostMain['userPostMains'] as $modelUserPostMainChild): ?>

                                                    <li id="image-<?= $modelUserPostMainChild['id'] ?>" class="work-item gallery-photo-review">
                                                        <div class="gallery-item review-post-gallery">
                                                            <div class="gallery-image">
                                                                <div class="work-image">

                                                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $modelUserPostMainChild['image'], 200, 200), ['class' => 'img-component']); ?>

                                                                </div>
                                                                <div class="work-caption">
                                                                    <div class="work-descr"><?= !empty($modelUserPostMainChild['text']) ? $modelUserPostMainChild['text'] : '' ?></div>
                                                                    <div class="work-descr">
                                                                        <a class="btn btn-d btn-small btn-xs btn-circle delete-image" href="<?= Yii::$app->urlManager->createUrl(['user-action/delete-photo', 'id' => $modelUserPostMainChild['id']]) ?>"><i class="fa fa-trash"></i></a>
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
                            </div>

                            <div class="form-group">

                                <?= $form->field($modelPost, '[photo]image[]')->widget(FileInput::classname(), [
                                    'options' => [
                                        'id' => 'post-photo-input',
                                        'accept' => 'image/*',
                                        'multiple' => true,
                                    ],
                                    'pluginOptions' => [
                                        'browseClass' => 'btn btn-d',
                                        'showRemove' => false,
                                        'showUpload' => false,
                                        'layoutTemplates' => [
                                            'footer' => '<h4><small class="file-caption-name" style="width:{width}">{caption}</small></h4>',
                                        ],
                                    ]
                                ]); ?>

                            </div>
                            <div class="form-group">

                                <?php
                                $socialMediaItems = [
                                    'facebook' => 'Berbagi ke Facebook',
                                ];

                                $options = [
                                    'class' => 'social-media-share-list',
                                    'separator' => '&nbsp;&nbsp;&nbsp;',
                                    'item' => function ($index, $label, $name, $checked, $value) {
                                        return '<label style="font-weight: normal;">' .
                                                Html::checkbox($name, $checked, [
                                                    'value' => $value,
                                                    'class' => $value . '-review-share-trigger icheck',
                                                ]) . ' ' . $label .
                                                '</label>';
                                    },
                                ];

                                echo Html::checkboxList('social_media_share', null, $socialMediaItems, $options) ?>

                            </div>
                            <div class="form-group">

                                <?= Html::submitButton('<i class="fa fa-share-square"></i> Post review', ['class' => 'btn btn-default btn-standard btn-round']) ?>

                                <?= Html::a('<i class="fa fa-times"></i> Cancel', null, ['id' => 'cancel-write-review', 'class' => 'btn btn-default btn-standard btn-round']) ?>

                            </div>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<div class="row mt-10">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <h4 class="mt-0 mb-0 inline-block">Review</h4>
            </div>

            <hr class="divider-w">

            <div class="box-content">
                <div class="review-section"></div>
            </div>
        </div>
    </div>
</div>

<ul id="container-temp-uploaded-photo" class="hidden">
    <li class="work-item gallery-photo-review">
        <div class="gallery-item review-post-gallery">
            <div class="gallery-image">
                <div class="work-image"></div>
                <div class="work-caption">
                    <div class="work-descr"></div>
                </div>
            </div>
        </div>
    </li>
</ul>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

frontend\components\RatingColor::widget();
frontend\components\Readmore::widget();

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var prevReview;

    $("#write-review-container").hide();
    $("#close-review-container").hide();

    $("#write-review-trigger").on("focusin", function(event) {
        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl(['redirect/write-review']) . '",
            success: function(response) {
                thisObj.fadeOut(100, function() {
                    $("#write-review-container").fadeIn();
                    $("#close-review-container").fadeIn();
                });
            },
            error: function(xhr, ajaxOptions, thrownError) {
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    $(".review-option").hide();

    $(".review-option-toggle").on("click", function() {
        $(".review-option").slideToggle();
    });

    $(".edit-my-review-trigger").on("click", function(event) {
        $("#edit-review-container").fadeOut(100, function() {
            prevReview = $("#post-review-text").val();

            if ($("#form-review-uploaded-photo").find("li").length == 0) {
                $("#form-photos-review-container").parent().addClass("hidden");
            } else {
                $("#form-photos-review-container").parent().removeClass("hidden");
            }

            $("#write-review-container").fadeIn();
            $("#close-review-container").fadeIn();
        });
    });

    $(".delete-my-review-trigger").on("click", function(event) {
        var form = $("form#review-form").serialize();

        $.ajax({
            cache: false,
            type: "POST",
            data: form,
            url: $(this).attr("href"),
            success: function(response) {
                if (response.status == "sukses") {
                    var totalUserPost = parseInt($(".total-review").html());

                    if (response.publish) {
                        $(".delete-my-review-trigger").html("<i class=\"fa fa-trash-alt\"></i> Delete").attr("href", response.deleteUrlReview);
                        $(".total-review").html(totalUserPost + 1);
                    } else {
                        $(".delete-my-review-trigger").html("<i class=\"fa fa-undo-alt\"></i> Undo").attr("href", response.undoUrlReview);
                        $(".total-review").html(totalUserPost - 1);
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

    $("#write-review-shortcut").on("click", function(event) {
        if (!$("a[aria-controls=\"view-review\"]").parent().hasClass("active")) {
            $("a[aria-controls=\"view-review\"]").tab("show");

            $("a[aria-controls=\"view-review\"]").on("shown.bs.tab", function (e) {
                $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
                $(this).off("shown.bs.tab");
            });
        } else {
            $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
        }

        return false;
    });

    $("#write-review-shortcut-xs").on("click", function(event) {
        if (!$("a[aria-controls=\"view-review\"]").parent().hasClass("active")) {
            $("a[aria-controls=\"view-review\"]").tab("show");

            $("a[aria-controls=\"view-review\"]").on("shown.bs.tab", function (e) {
                $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
                $(this).off("shown.bs.tab");
            });
        } else {
            $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
        }

        return false;
    });

    $("#close-review-container > a, #cancel-write-review").on("click", function(event) {
        $("#write-review-container, #close-review-container").fadeOut(100, function() {
            $("#edit-review-container").fadeIn();
            $("#write-review-trigger").fadeIn();
            $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
        });

        var tempOverallRating = $(".temp-overall-rating").val();

        if (tempOverallRating == "") {
            $("#overall-rating").rating("reset");
            $(".rating-overall").children("span").html(parseFloat(parseFloat($("#overall-rating").val()).toFixed(1)));
        } else {
            $("#overall-rating").rating("update", tempOverallRating);
            $(".rating-overall").children("span").html(parseFloat(parseFloat(tempOverallRating).toFixed(1)));
        }

        $(".rating-component-id").each(function() {
            if ($(".temp-rating-" + $(this).val()).val() == "") {
                $(this).parent().find("#rating-" + $(this).val() + "").rating("reset");
                $("#post-review-rating-" + $(this).val() + "").val($("#rating-" + $(this).val() + "").val());
            } else {
                $(this).parent().find("#rating-" + $(this).val() + "").rating("update", $(".temp-rating-" + $(this).val()).val());
                $("#post-review-rating-" + $(this).val() + "").val($(".temp-rating-" + $(this).val()).val());
            }
        });

        $("#post-review-text").val(prevReview);
        $("#post-photo-input").fileinput("clear");

        $(".facebook-review-share-trigger").iCheck("uncheck");

        return false;
    });

    $("#overall-rating").on("change", function() {
        var thisObj = $(this);

        $(".rating-component-id").each(function() {
            $(this).parent().find("#rating-" + $(this).val() + "").rating("update", thisObj.val());

            $(this).parent().find("#post-review-rating-" + $(this).val() + "").val(thisObj.val());
        });
    });

    function setOverall() {
        var overall = 0;

        $(".rating-component-id").each(function() {
            var rating = parseInt($(this).parent().find("#rating-" + $(this).val() + "").val());

            overall += rating;
        });

        overall = overall / parseInt($(".rating-component-id").length);

        if (!isNaN(overall)) {
            $("#overall-rating").rating("update", overall);
            $(".rating-overall").children("span").html(parseFloat(overall.toFixed(1)));
        } else {
            $(".rating-overall").children("span").html("0");
        }
    }

    $(".rating-component-id").each(function() {
        var thisObj = $(this);

        thisObj.parent().find("#rating-" + thisObj.val() + "").on("change", function() {
            $("#post-review-rating-" + thisObj.val() + "").val($(this).val());

            setOverall();
        });
    });

    var myUserPostMainId = $("#edit-review-container").find(".my-user-post-main-id").val();

    $("form#review-form").on("beforeSubmit", function(event) {
        var thisObj = $(this);

        var formData = new FormData(this);

        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            type: "POST",
            data: formData,
            url: thisObj.attr("action"),
            beforeSend: function(xhr) {
                $("#title-write-review").siblings(".overlay").show();
                $("#title-write-review").siblings(".loading-img").show();
            },
            success: function(response) {
                $("#post-photo-input").fileinput("clear");

                if (response.status == "sukses") {
                    if (!response.updated) {
                        $(".total-review").html(parseInt($(".total-review").html()) + 1);
                    }

                    prevReview = response.userPostMain.text;
                    var newOverall = $("#write-review-container").find(".rating-overall").text();
                    var reviewUploadedPhoto = $("#edit-review-container").find("#review-uploaded-photo");
                    var formReviewUploadedPhoto = $("#write-review-container").find("#form-review-uploaded-photo");

                    $("#edit-review-container").find(".my-rating").children().html(parseFloat(newOverall).toFixed(1));

                    $("#edit-review-container").find(".my-review-user-name").html(response.user);
                    $("#edit-review-container").find(".my-review-created").html(response.userCreated);
                    $("#edit-review-container").find(".my-review-description").html(response.userPostMain.text);

                    readmoreText({
                        element: $(".my-review-description"),
                        minChars: 500,
                        ellipsesText: " . . . ",
                        moreText: "See more",
                        lessText: "See less",
                    });

                    $.each(response.userPostMainPhoto, function(i, item) {
                        var cloneImageReviewContainer = $("#container-temp-uploaded-photo").find("li").clone();
                        var cloneImageFormContainer = $("#container-temp-uploaded-photo").find("li").clone();

                        cloneImageReviewContainer.attr("id", "image-" + response.userPostMainPhoto[i].id);
                        cloneImageReviewContainer.find(".review-post-gallery").find(".work-image").html("<img class=\"img-component\" src=\"" + response.userPostMainPhoto[i].image + "\" title=\"\">");
                        cloneImageReviewContainer.find(".review-post-gallery").find(".work-caption").find(".work-descr").html("<a class=\"btn btn-d btn-small btn-xs btn-circle show-image\" href=\"" + response.userPostMainPhoto[i].image.replace("200x200", "") + "\"><i class=\"fa fa-search\"></i></a>");
                        cloneImageReviewContainer.appendTo(reviewUploadedPhoto);

                        cloneImageFormContainer.attr("id", "image-" + response.userPostMainPhoto[i].id);
                        cloneImageFormContainer.find(".review-post-gallery").find(".work-image").html("<img class=\"img-component\" src=\"" + response.userPostMainPhoto[i].image + "\" title=\"\">");
                        cloneImageFormContainer.find(".review-post-gallery").find(".work-caption").find(".work-descr").html("<a class=\"btn btn-d btn-small btn-xs btn-circle delete-image\" href=\"" + response.deleteUrlPhoto + "?id=" + response.userPostMainPhoto[i].id + "\"><i class=\"fa fa-trash\"></i></a>");
                        cloneImageFormContainer.appendTo(formReviewUploadedPhoto);
                    });

                    $(".delete-my-review-trigger").attr("href", response.deleteUrlReview);

                    var tempOverall = 0;

                    $(".rating-component-id").each(function() {
                        var tempRating = parseInt($("#post-review-rating-" + $(this).val() + "").val());

                        tempOverall += tempRating;

                        $(".temp-rating-" + $(this).val() + "").val(tempRating);
                    });

                    $(".temp-overall-rating").val(tempOverall / parseInt($(".rating-component-id").length));

                    getPlaceVote($("#business_id").val());
                    getUserPhoto($("#business_id").val());

                    ratingColor($(".my-rating"), "a");

                    $("#form-photos-review-container").parent().removeClass("hidden");

                    $("#edit-review-container").find(".my-total-photos-review").html(parseInt($("#edit-review-container").find(".my-total-photos-review").html()) + parseInt(response.userPostMainPhoto.length));

                    $("#edit-review-container").find(".my-total-comments-review").addClass("total-" + response.userPostMain.id + "-comments-review").removeClass("my-total-comments-review");

                    $("#form-review-uploaded-photo .review-post-gallery").magnificPopup({
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

                    $("#review-uploaded-photo .review-post-gallery").magnificPopup({
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

                    $("#title-write-review").find("h4").html("Your Review");
                    $("#edit-review-container").removeClass("hidden");
                    $("#write-review-trigger").addClass("hidden");

                    $("#write-review-container, #close-review-container").fadeOut(100, function() {
                        $("#edit-review-container").fadeIn();
                        $("#write-review-trigger").fadeIn();
                        $("html, body").animate({ scrollTop: $("#title-write-review").offset().top }, "slow");
                    });

                    $(".facebook-review-share-trigger").iCheck("uncheck");

                    if ($.trim(response.socialShare)){
                        $.each(response.socialShare, function(socialName, value) {
                            if (socialName === "facebook" && response.socialShare[socialName]) {
                                var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + response.userPostMain.id;
                                var title = "Rating " + $("#edit-review-container").find(".my-rating").text().trim() + " untuk " + $(".business-name").text().trim();
                                var description = response.userPostMain.text;
                                var image = window.location.protocol + "//" + window.location.hostname + $("#form-review-uploaded-photo li").eq(0).find(".work-image").children().attr("src");

                                facebookShare({
                                    ogUrl: url,
                                    ogTitle: title,
                                    ogDescription: description,
                                    ogImage: image,
                                    type: "Review"
                                });
                            }
                        });
                    }

                    messageResponse(response.icon, response.title, response.message, response.type);
                } else {
                    messageResponse(response.icon, response.title, response.message, response.type);
                }

                $("#title-write-review").siblings(".overlay").hide();
                $("#title-write-review").siblings(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                $("#title-write-review").siblings(".overlay").hide();
                $("#title-write-review").siblings(".loading-img").hide();
            }
        });

        return false;
    });

    function getUserPost(business_id) {
        $.ajax({
            cache: false,
            type: "GET",
            data: {
                "business_id": business_id
            },
            url: "' . Yii::$app->urlManager->createUrl(['data/post-review']) . '",
            success: function(response) {
                $(".review-section").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPost($("#business_id").val());

    function getPlaceVote(business_id) {
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "business_id": business_id
            },
            url: "' . Yii::$app->urlManager->createUrl(['data/business-rating']) . '",
            success: function(response) {
                $(".business-rating").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    $("#edit-review-container").find(".my-likes-review-trigger").on("click", function() {
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": myUserPostMainId
            },
            url: "' . Yii::$app->urlManager->createUrl(['action/submit-likes']) . '",
            success: function(response) {
                if (response.status == "sukses") {
                    var loveValue = parseInt($("#edit-review-container").find(".my-likes-review-trigger").find("span.my-total-likes-review").html());

                    if(response.is_active) {
                        $("#edit-review-container").find(".my-likes-review-trigger").addClass("selected");
                        $("#edit-review-container").find("span.my-total-likes-review").html((loveValue + 1).toString());
                    } else {
                        $("#edit-review-container").find(".my-likes-review-trigger").removeClass("selected");
                        $("#edit-review-container").find("span.my-total-likes-review").html((loveValue - 1).toString());
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

    $("#edit-review-container").find("#my-comments-review-container").hide();
    $("#edit-review-container").find("#my-photos-review-container").hide();

    $("#edit-review-container").find(".my-comments-review-trigger").on("click", function() {
        $("#edit-review-container").find("#my-comments-review-container").slideToggle();

        return false;
    });

    $("#edit-review-container").find("#input-my-comments-review").on("keypress", function(event) {
        if(event.which == 13 && $(this).val().trim()) {
            var data = {
                "user_post_main_id": myUserPostMainId,
                "text": $(this).val(),
            };

            $.ajax({
                type: "POST",
                data: data,
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {
                    $(".my-comment-section").siblings(".overlay").show();
                    $(".my-comment-section").siblings(".loading-img").show();
                },
                success: function(response) {
                    if (response.status == "sukses") {
                        $("#input-my-comments-review").val("");

                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": response.user_post_main_id
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                            success: function(response) {
                                $(".my-comment-section").html(response);
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                            }
                        });
                    } else {
                        messageResponse(response.icon, response.title, response.message, response.type);
                    }

                    $(".my-comment-section").siblings(".overlay").hide();
                    $(".my-comment-section").siblings(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                }
            });
        }
    });

    $("#edit-review-container").find(".my-photos-review-trigger").on("click", function() {
        if ($("#edit-review-container").find("#my-photos-review-container").find(".gallery-photo-review").length) {
            $("#edit-review-container").find("#my-photos-review-container").toggle(500);
        }

        return false;
    });

    $("#review-uploaded-photo .review-post-gallery").magnificPopup({
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

    $("#form-review-uploaded-photo .review-post-gallery").magnificPopup({
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

    $("#form-photos-review-container").on("click", ".delete-image", function() {
        $("#modal-confirmation").modal("show");

        $("#modal-confirmation").find("#btn-delete").data("href", $(this).attr("href"));

        return false;
    });

    $("#modal-confirmation").find("#btn-delete").on("click", function() {
        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).data("href"),
            success: function(response) {
                $("#modal-confirmation").modal("hide");

                if (response.status == "sukses") {
                    getUserPhoto($("#business_id").val());

                    $("#review-uploaded-photo").find("li#image-" + response.id).remove();
                    $("#form-review-uploaded-photo").find("li#image-" + response.id).remove();

                    if ($("#form-review-uploaded-photo").find("li").length == 0) {
                        $("#form-photos-review-container").parent().addClass("hidden");
                    }

                    $("#edit-review-container").find(".my-total-photos-review").html(parseInt($("#edit-review-container").find(".my-total-photos-review").html()) - 1);

                    messageResponse(response.icon, response.title, response.message, response.type);
                } else {
                    messageResponse(response.icon, response.title, response.message, response.type);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    ratingColor($(".my-rating"), "a");

    readmoreText({
        element: $(".my-review-description"),
        minChars: 500,
        ellipsesText: " . . . ",
        moreText: "See more",
        lessText: "See less",
    });

    $(".total-review").html(' . $reviewTotal . ');

    $("#my-rating-popover").popoverButton({
        trigger: "hover",
        placement: "right right-top",
        target: "#my-popover-container",
        content: function () {
            var content = $("#my-popover-container").find(".popover-content").html();

            return content;
        }
    });

    $("#my-rating-popover").on("mouseenter.popoverX", function(event) {
        $(".rating-component-id").each(function() {
            if ($(".temp-rating-" + $(this).val()).val() == "") {
                $("#my-popover-container").find(".popover-content").find("#my-rating-" + $(this).val() + "").rating("update", $("#write-review-container").find(".star-rating").find("#rating-" + $(this).val() + "").val());
            } else {
                $("#my-popover-container").find(".popover-content").find("#my-rating-" + $(this).val() + "").rating("update", $("#write-review-container").find(".star-rating").find(".temp-rating-" + $(this).val() + "").val());
            }
        });

        return false;
    });
';

if (!empty($dataUserVoteReview['overallValue'])) {

    $jscript .= '
        var overall = ' . $dataUserVoteReview['overallValue'] . ';

        $(".rating-overall").html(parseFloat(overall.toFixed(1)));
    ';
}

$this->registerJs($jscript); ?>