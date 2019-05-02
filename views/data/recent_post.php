<?php

use yii\widgets\LinkPager;
use yii\widgets\ListView;

/* @var $dataProviderUserPostMain yii\data\ActiveDataProvider */

echo ListView::widget([
    'id' => 'recent-activity',
    'dataProvider' => $dataProviderUserPostMain,
    'itemView' => '_recent_post',
    'layout' => '
        <div class="row">
            {items}
            <div>
                <div class="clearfix"></div>
                <div class="col-lg-12 align-center">{pager}</div>
            </div>
        </div>
    ',
    'pager' => [
        'class' => LinkPager::class,
        'maxButtonCount' => 0,
        'prevPageLabel' => false,
        'nextPageLabel' => \Yii::t('app', 'Load More'),
        'options' => ['id' => 'pagination-recent-post', 'class' => 'pagination'],
    ]
]); ?>