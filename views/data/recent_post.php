<?php
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/* @var $dataProviderUserPostMain yii\data\ActiveDataProvider */ ?>

<?= ListView::widget([
    'id' => 'recent-activity',
    'dataProvider' => $dataProviderUserPostMain,
    'itemView' => '_recent_post',
    'layout' => '
        <div class="row">
            {items}
            <div>
                <div class="clearfix"></div>
                <div class="col-lg-12 align-center">{pager}</div>
            <div>
        </div>
    ',
    'pager' => [
        'class' => LinkPager::class,
        'maxButtonCount' => 0,
        'prevPageLabel' => false,
        'nextPageLabel' => Yii::t('app', 'Load More'),
        'options' => ['id' => 'pagination-recent-post', 'class' => 'pagination'],
    ]
]); ?>