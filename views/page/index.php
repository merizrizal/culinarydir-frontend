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
            <div class="container mt-40">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                        <div class="titan-title-tagline mb-20">Cari tempat makan favoritmu</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">

                        <!--<div class="overlay-coming-soon">

                            <div class="titan-caption">
                                <div class="caption-content">
                                    <div class="font-alt mb-30 titan-title-size-3">Asikmakan</div>
                                    <div class="font-alt mb-30 titan-title-size-4">Coming Soon</div>
                                    <div class="font-alt">Website masih dalam tahap development</div>
                                    <div class="font-alt mt-10">
                                        <a class="section-scroll text-center text-white" href="#footer">
                                            <i class="fa fa-angle-double-down fa-4x animate-bounce"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>-->

                        <?php
                        $appComponent = new AppComponent();
                        echo $appComponent->search(); ?>

                    </div>
                </div>
            </div>
            <div class="row mt-40">				
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                    <a class="section-scroll text-center text-white" href="#footer">
                        <i class="fa fa-angle-double-down fa-4x animate-bounce"></i>
                    </a>
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

<?= $appComponent->searchJsComponent(); ?>

<?php
$csscript = '
    .overlay-coming-soon {
        z-index: 1010;
        background: rgba(100, 100, 100, 0.7);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
';

$this->registerCss($csscript); ?>
