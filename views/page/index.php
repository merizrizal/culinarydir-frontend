<?php
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
        <div class="view" id="recent-activity"></div>
    </div>
</section>

<?= $appComponent->searchJsComponent(); ?>

<?php
$jscript = '
    $.ajax({
        cache: false,
        type: "GET",
        url: "' . Yii::$app->urlManager->createUrl(['data/recent-post']) . '",
        success: function(response) {

            $(".view").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });
';

$this->registerJs($jscript); ?>
