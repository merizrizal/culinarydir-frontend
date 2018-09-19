<?php
use yii\widgets\ListView;
use yii\widgets\LinkPager;
use frontend\components\AppComponent;

/* @var $this yii\web\View */

$this->title = 'Mau Makan Asik, Ya Asikmakan';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Bisnis Kuliner Di Bandung - Temukan Tempat Kuliner Terbaik Favorit Anda Di Asikmakan'
]); ?>

<section class="home-section home-full-height bg-dark visible-lg visible-md visible-sm" id="home" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
    <div class="titan-caption">
        <div class="caption-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                        <div class="titan-title-tagline mb-10">Cari tempat makan favoritmu</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">

                        <?php
                        $appComponent = new AppComponent();
                        echo $appComponent->search(); ?>

                    </div>
                </div>
                <div class="row mt-40">
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                        <a class="section-scroll text-center text-white" href="#recent-activity">
                            <i class="fa fa-angle-double-down fa-4x animate-bounce"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                        <a class="section-scroll text-center text-white" href="#recent-activity">
                            <h5 class="font-alt"><?= Yii::t('app', 'Recent Activity') ?></h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="module-small visible-tab" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
                <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                <?= $appComponent->search([
                    'id' => 'tab-search'
                ]); ?>

            </div>
        </div>
    </div>
</section>

<section class="module-small visible-xs" data-background="<?= Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-home-bg.jpg' ?>">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
                <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">

                <?= $appComponent->search([
                    'id' => 'xs-search'
                ]); ?>

            </div>
        </div>
    </div>
</section>

<section class="module-extra-small in-result bg-main">
    <div class="container detail">
        <div class="view" id="recent-activity">

            <div class="row mt-10 mb-20">
                <div class="col-lg-12 font-alt"> <?= Yii::t('app', 'Recent Activity'); ?> </div>
            </div>

            <?= ListView::widget([
                'dataProvider' => $dataProviderUserPostMain,
                'itemView' => '@frontend/views/data/_recent_post',
                'layout' => '
                    <div class="row">
                        {items}
                        <div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12">{pager}</div>
                        <div>
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

        </div>
    </div>
</section>

<?= $appComponent->searchJsComponent(); ?>

<?php
$jscript = '
    $("#pagination-recent-post li.next a").on("click", function() {

        var thisObj = $(this);

        return false;
    });
';

$this->registerJs($jscript); ?>
