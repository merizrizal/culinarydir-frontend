<?php

use yii\helpers\Html;
use sycomponent\Tools;
use common\components\Helper;

/* @var $this yii\web\View */
/* @var $modelUserPostComment core\models\UserPostComment */ 
/* @var $userPostId frontend\controllers\DataController */

if (!empty($modelUserPostComment)): ?>

    <div class="comment-container">

        <?php
        foreach ($modelUserPostComment as $dataUserPostComment): ?>

            <div class="comment-post">
                <div class="row mb-10">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="widget">
                            <div class="widget-comments-image">

                                <?php
                                $img = Yii::getAlias('@uploadsUrl') . '/img/user/default-avatar.png';

                                if (!empty($dataUserPostComment['user']['image'])) {

                                    $img = Yii::$app->params['loadUserImage'] . $dataUserPostComment['user']['image'] . '&w=64&h=64';
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

<?php
endif;

$jscript = '
    var commentCount = ' . (!empty($modelUserPostComment) ? count($modelUserPostComment) : '0') . ';
';

$this->registerJs($jscript); ?>