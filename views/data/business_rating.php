<?php

use kartik\rating\StarRating; ?>

<div class="row">
    <div class="col-sm-6 col-xs-6 text-center">
        <div class="rating">
            <h2 class="mt-0 mb-0"><span class="label label-success pt-10"><?= number_format((float) $modelBusinessDetail['vote_value'], 1, '.', '') ?></span></h2>
            <?= $modelBusinessDetail['voters'] ?> votes
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        <h4 class="points-label"><?= number_format((float) !empty($modelBusinessDetail['vote_points']) && !empty($modelRatingComponent) ? ($modelBusinessDetail['vote_points'] / count($modelRatingComponent)) : 0, 1, '.', '') ?></h4> Points
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

                        $ratingValue = !empty($dataBusinessDetailVote['vote_value']) && !empty($modelBusinessDetail['voters']) ? ($dataBusinessDetailVote['vote_value'] / $modelBusinessDetail['voters']) : 0; ?>

                        <li>
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 business-rating-star text-right">

                                    <?= StarRating::widget([
                                        'id' => 'business-' . strtolower($dataBusinessDetailVote['ratingComponent']['name']) . '-rating',
                                        'name' => 'business-' . strtolower($dataBusinessDetailVote['ratingComponent']['name']) . '-rating',
                                        'value' => !empty($ratingValue) ? $ratingValue : 0,
                                        'pluginOptions' => [
                                            'displayOnly' => true,
                                            'filledStar' => '<span class="aicon aicon-star-full"></span>',
                                            'emptyStar' => '<span class="aicon aicon-star-empty"></span>',
                                        ]
                                    ]); ?>

                                </div>

                                <div class="col-sm-6 col-xs-6 business-rating-components">

                                    <?= number_format((float)$ratingValue, 1, '.', '') . ' &nbsp; ' . $dataBusinessDetailVote['ratingComponent']['name'] ?>

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
                                        ]
                                    ]); ?>

                                </div>

                                <div class="col-sm-6 col-xs-6 business-rating-components">

                                    <?= 0 . ' &nbsp; ' . $dataRatingComponent['name'] ?>

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
$csscript = '
    .business-rating-components {
        padding-top: 4px;
    }
';

$this->registerCss($csscript);

frontend\components\RatingColor::widget();

$jscript = '
    ratingColor($(".rating"), "span");
';

$this->registerJs($jscript); ?>