<?php

use yii\helpers\Html;
use sycomponent\Tools; ?>

<?php
$jscript = '';

if (!empty($userPostComment)):

    foreach ($userPostComment as $userPostId => $modelUserPostComment): ?>

        <div class="post-<?= $userPostId ?>-comment-container">

            <?php
            foreach ($modelUserPostComment as $dataUserPostComment): ?>

                <div class="comment-post">
                    <div class="row mb-10">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="widget">
                                <div class="widget-comments-image">

                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . (!empty($dataUserPostComment['user']['image']) ? Tools::thumb('/img/user/', $dataUserPostComment['user']['image'], 200, 200) : '/img/user/default-avatar.png'), ['class' => 'img-responsive img-circle img-comment-thumb img-component']); ?>

                                </div>

                                <div class="widget-comments-body">
                                    <strong><?= Html::a($dataUserPostComment['user']['full_name'], Yii::$app->urlManager->createUrl(['user/user-profile', 'user' => $dataUserPostComment['user']['username']])); ?>&nbsp;&nbsp;&nbsp;</strong>
                                    <small><?= Yii::$app->formatter->asRelativeTime($dataUserPostComment['created_at']) ?></small>
                                    <br>
                                    <p class="review-description">

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

    <?php
    $jscript .= '
        $(".total-' . $userPostId . '-comments-review").html("' . (!empty($modelUserPostComment) ? count($modelUserPostComment) : '0') . '");

        $(".comment-' . $userPostId . '-section").html($(".post-' . $userPostId . '-comment-container").html());
    ';

    endforeach;
endif; ?>

<?php
$this->registerJs($jscript); ?>