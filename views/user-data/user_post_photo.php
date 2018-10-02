<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */
/* @var $startItem int */
/* @var $endItem int */
/* @var $totalCount int */
/* @var $modelUserPostMainPhoto core\models\UserPostMain */

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-user-photo a',
    'options' => ['id' => 'pjax-user-photo-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-user-photo', 'class' => 'pagination'],
]); ?>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<div class="row" style="position: relative;">
    <div class="col-md-12 user-post-photo-container">
    
		<div class="overlay" style="display: none;"></div>
		<div class="loading-img" style="display: none;"></div>

        <ul class="works-grid works-grid-gut works-grid-4" id="photo-gallery">

            <?php
            if (!empty($modelUserPostMainPhoto)):

                foreach ($modelUserPostMainPhoto as $dataUserPostMainPhoto): ?>

                    <li class="work-item">

                        <?= Html::hiddenInput('business_name', $dataUserPostMainPhoto['business']['name'], ['class' => 'business-name']) ?>

                        <div class="gallery-item place-gallery">
                            <div class="gallery-image">
                                <div class="work-image">
                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMainPhoto['image'], 200, 200), ['class' => 'img-component', 'data-id' => $dataUserPostMainPhoto['id']]) ?>
                                </div>
                                <div class="work-caption">
                                    <div class="work-descr hidden-xs"><?= !empty($dataUserPostMainPhoto['text']) ? $dataUserPostMainPhoto['text'] : '' ?></div>
                                    <div class="work-descr">
                                        <a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $dataUserPostMainPhoto['image'] ?>"><i class="fa fa-search"></i></a>
                                        <a class="btn btn-d btn-small btn-xs btn-circle share-image-<?= $dataUserPostMainPhoto['id'] ?>-trigger"><i class="fa fa-share-alt"></i></a>

                                        <?php
                                        if (!empty(Yii::$app->user->getIdentity()->id) && Yii::$app->user->getIdentity()->id == $dataUserPostMainPhoto['user_id']) {

                                            echo Html::a('<i class="fa fa-trash"></i>', ['user-action/delete-photo', 'id' => $dataUserPostMainPhoto['id']], ['class' => 'btn btn-d btn-small btn-xs btn-circle delete-image']);
                                        } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                <?php
                endforeach;
            endif; ?>

        </ul>
    </div>
</div>

<div class="row mt-20 mb-10">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= Yii::t('app', 'Showing {startItem} - {endItem} of {totalCount} results', ['startItem' => $startItem, 'endItem' => $endItem, 'totalCount' => $totalCount]) ?>

    </div>
    <div class="col-sm-6 visible-lg visible-md visible-sm text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-tab-6 visible-tab text-right">

        <?= $linkPager; ?>

    </div>
    <div class="col-xs-12 visible-xs">

        <?= $linkPager; ?>

    </div>
</div>

<?php
frontend\components\GrowlCustom::widget();
frontend\components\FacebookShare::widget();

$jscript = '
    $("#photo-gallery .place-gallery").magnificPopup({

        delegate: "a.show-image",
        type: "image",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        },
        image: {
            titleSrc: "title",
            tError: "The image could not be loaded."
        }
    });

    $(".delete-image").on("click", function() {

        $("#modal-confirmation").modal("show");

        $("#modal-confirmation").find("#btn-delete").data("href", $(this).attr("href"));

        return false;
    });

    $("#photo-gallery").find(".work-item").each(function() {

        var thisObj = $(this);
        var photoId = $(this).find(".work-image").children().data("id");

        $(this).find(".share-image-" + photoId + "-trigger").on("click", function() {

            var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/photo']) . '/" + photoId;
            var title = "Foto untuk " + thisObj.find(".business-name").val();
            var description = thisObj.find(".photo-caption").text();
            var image = window.location.protocol + "//" + window.location.hostname + thisObj.find(".work-image").children().attr("src");

            facebookShare({
                ogUrl: url,
                ogTitle: title,
                ogDescription: description,
                ogImage: image,
                type: "Foto"
            });

            return false;
        });
    });

    $(".total-user-photo").html("' . $totalCount . '");

    $("#pjax-user-photo-container").on("pjax:send", function() {

        $(".user-post-photo-container").children(".overlay").show();
        $(".user-post-photo-container").children(".loading-img").show();
    });

    $("#pjax-user-photo-container").on("pjax:complete", function() {

        $(".user-post-photo-container").children(".overlay").hide();
        $(".user-post-photo-container").children(".loading-img").hide();
    });

    $("#pjax-user-photo-container").on("pjax:error", function (event) {

        event.preventDefault();
    });
';

$this->registerJs($jscript);

Pjax::end(); ?>