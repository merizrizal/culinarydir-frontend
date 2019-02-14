<?php
use kartik\rating\StarRating; 

/* @var $this yii\web\View */
/* @var $modelBusinessDetail core\models\BusinessDetail */
/* @var $modelBusinessDetailVote core\models\BusinessDetailVote */
/* @var $modelRatingComponent core\models\RatingComponent */ ?>

<div class="row">
    <div class="col-sm-6 col-xs-6 text-center">
        <div class="rating">
            <h2 class="mt-0 mb-0"><span class="label label-success pt-10"><?= number_format(!empty($modelBusinessDetail['vote_value']) ? $modelBusinessDetail['vote_value'] : 0, 1) ?></span></h2>
            <?= Yii::t('app', '{value, plural, =0{# Vote} =1{# Vote} other{# Votes}}', ['value' => !empty($modelBusinessDetail['voters']) ? $modelBusinessDetail['voters'] : 0]) ?>
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        <h4 class="points-label"><?= number_format(!empty($modelBusinessDetail['vote_points']) ? $modelBusinessDetail['vote_points'] : 0, 1) ?></h4> Points
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="widget star-rating">
            <ul class="icon-list">

                <?php
                if (!empty($modelBusinessDetailVote)):

                    $ratingComponent = [];

                    foreach ($modelBusinessDetailVote as $dataBusinessDetailVote){

                        if (!empty($dataBusinessDetailVote['ratingComponent'])) {

                            $ratingComponent[$dataBusinessDetailVote['ratingComponent']['order']] = $dataBusinessDetailVote;
                        }
                    }

                    ksort($ratingComponent);

                    foreach($ratingComponent as $dataBusinessDetailVote):

                        $ratingValue = !empty($dataBusinessDetailVote['vote_value']) ? $dataBusinessDetailVote['vote_value'] : 0; ?>

                        <li>
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 business-rating-star text-right">

                                    <?= StarRating::widget([
                                        'id' => 'business-' . strtolower($dataBusinessDetailVote['ratingComponent']['name']) . '-rating',
                                        'name' => 'business-' . strtolower($dataBusinessDetailVote['ratingComponent']['name']) . '-rating',
                                        'value' => $ratingValue,
                                        'pluginOptions' => [
                                            'displayOnly' => true,
                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                            'showCaption' => false,
                                        ]
                                    ]); ?>

                                </div>

                                <div class="col-sm-6 col-xs-6 business-rating-components">

                                    <?= number_format($ratingValue, 1) . ' &nbsp; ' . Yii::t('app', $dataBusinessDetailVote['ratingComponent']['name']) ?>

                                </div>
                            </div>
                        </li>

                    <?php
                    endforeach;

                else:

                    foreach($modelRatingComponent as $dataRatingComponent): ?>

                        <li>
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 business-rating-star text-right">

                                    <?= StarRating::widget([
                                        'id' => 'business-' . strtolower($dataRatingComponent['name']) . '-rating',
                                        'name' => 'business-' . strtolower($dataRatingComponent['name']) . '-rating',
                                        'value' => 0,
                                        'pluginOptions' => [
                                            'displayOnly' => true,
                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                            'showCaption' => false,
                                        ]
                                    ]); ?>

                                </div>

                                <div class="col-sm-6 col-xs-6 business-rating-components">

                                    <?= 0 . ' &nbsp; ' . Yii::t('app', $dataRatingComponent['name']) ?>

                                </div>
                            </div>
                        </li>

                    <?php
                    endforeach;
                endif;?>

            </ul>
        </div>
    </div>
</div>

<?php
$cssScript = '
    .business-rating-components {
        padding-top: 4px;
    }
';

$this->registerCss($cssScript);

frontend\components\RatingColor::widget();

$jscript = '
    ratingColor($(".rating"), "span");
';

$this->registerJs($jscript); ?>