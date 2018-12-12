<?php 

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use frontend\components\AppComponent;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $keyword array */

dosamigos\gallery\GalleryAsset::register($this);
dosamigos\gallery\DosamigosAsset::register($this);

$this->title = Yii::t('app', 'Search Result') . ' - Order Online';

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$appComponent = new AppComponent();

$background = Yii::$app->urlManager->baseUrl . '/media/img/asikmakan-result-bg.jpeg';?>

<div class="main">

	<section class="module-small visible-lg visible-md visible-sm" data-background="<?= $background ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12">

                    <?= $appComponent->search([
                        'keyword' => $keyword,
                    ]); ?>

                </div>
            </div>
        </div>
    </section>
    
    <section class="module-small result-order-search" data-background="<?= $background ?>">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <?= $appComponent->search([
                        'keyword' => $keyword,
                        'id' => 'search'
                    ]); ?>

                </div>
            </div>
        </div>
    </section>
    
    <section class="module-extra-small in-result bg-main">
    	<div class="container">
    		<div class="row">
    			<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-7">
    			
    			</div>
    			<div class="col-xs-5">

                    <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-toggle visible-tab']) ?>
                    <?= Html::button('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-round btn-default btn-search-toggle visible-xs']) ?>

                </div>
    		</div>
    	</div>
    	
    	<div class="result-order"></div>
    </section>

</div>

<?= $appComponent->searchJsComponent(); ?>

<?php
GrowlCustom::widget();
frontend\components\RatingColor::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $(".result-order-search").hide();

    $.ajax({
        cache: false,
        type: "GET",
        data: ' . Json::encode(Yii::$app->request->get()) . ',
        url: "' . Yii::$app->urlManager->createUrl(['data/result-order']) . '",
        success: function(response) {

            $(".result-order").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });

    $(".btn-search-toggle").on("click", function() {

        $(".result-order-search").toggle();
    });

    $(".result-list").on("click", ".popover-tag", function() {

        return false;
    });
';

$this->registerJs($jscript); ?>