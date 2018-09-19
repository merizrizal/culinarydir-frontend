<?php
use yii\widgets\ListView;
use yii\widgets\LinkPager; ?>

<?= ListView::widget([
    'dataProvider' => $dataProviderUserPostMain,
    'itemView' => '_recent_post',
    'layout' => '
        <div class="row">
            {items}
            <div class="clearfix"></div>
            <div class="col-lg-12">{pager}</div>
        </div>
    ',
    'pager' => [
        'class' => LinkPager::class,
        'maxButtonCount' => 0,
        'prevPageLabel' => false,
        'nextPageLabel' => Yii::t('app', 'Next'),
        'linkOptions' => [
            'class' => 'recent-post',
        ],
        'options' => ['id' => 'pagination-recent-post', 'class' => 'pagination'],
    ]
]); ?>