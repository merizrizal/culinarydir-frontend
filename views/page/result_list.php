<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\View;
use frontend\components\AppComponent;
use frontend\components\GrowlCustom;

/* @var $this yii\web\View */
/* @var $keyword array */
/* @var $params array */

$this->title = Yii::t('app', 'Search Result');

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'asik, makan, kuliner, bandung, jakarta'
]);

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com'
]);

$appComponent = new AppComponent(['showFacilityFilter' => true]); ?>

<div class="main">

    <section class="module-extra-small in-result bg-main">
        <div class="container">
        	<div class="row mb-20">
                <div class="col-md-10 col-md-offset-1 col-sm-12">

                    <?= $appComponent->search([
                        'keyword' => $keyword,
                        'type' => 'result-list-page'
                    ]); ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-7">

                    <?php
                    $urlResultMap = ArrayHelper::merge(['result-map'], $params);

                    echo Html::a('<i class="fa fa-list"></i> List', '', ['class' => 'btn btn-round btn-d btn-list']) . '&nbsp';
                    echo Html::a('<i class="fa fa-location-arrow"></i> ' . Yii::t('app', 'Map'), $urlResultMap, ['class' => 'btn btn-round btn-default btn-map']) ?>

                </div>
            </div>
        </div>

        <div class="result-list"></div>
    </section>
</div>

<?php
echo $appComponent->searchJsComponent($keyword);

GrowlCustom::widget();
frontend\components\RatingColor::widget();

$this->registerJs(GrowlCustom::messageResponse(), View::POS_HEAD);

$jscript = '
    $.ajax({
        cache: false,
        type: "GET",
        data: ' . Json::encode(Yii::$app->request->get()) . ',
        url: "' . Yii::$app->urlManager->createUrl(['data/result-list']) . '",
        success: function(response) {

            $(".result-list").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });

    $(".btn-list").on("click", function() {

        return false;
    });

    $(".result-list").on("click", ".popover-tag", function() {

        return false;
    });

    $(".result-list").on("click", ".love-button", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            url: "'. Yii::$app->urlManager->createUrl('action/submit-user-love').'",
            type: "POST",
            data: {
                "business_id": thisObj.data("id")
            },
            success: function(response) {

                if (response.success) {

                    if (response.is_active) {

                        thisObj.removeClass("far fa-heart").addClass("fas fa-heart");
                    } else {

                        thisObj.removeClass("fas fa-heart").addClass("far fa-heart");
                    }
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>