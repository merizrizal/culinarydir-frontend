<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use sycomponent\Tools;

Pjax::begin([
    'enablePushState' => false,
    'linkSelector' => '#pagination-photo a',
    'options' => ['id' => 'pjax-photo-container'],
    'timeout' => 7000,
]);

$linkPager = LinkPager::widget([
    'pagination' => $pagination,
    'maxButtonCount' => 5,
    'prevPageLabel' => false,
    'nextPageLabel' => false,
    'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
    'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
    'options' => ['id' => 'pagination-photo', 'class' => 'pagination'],
]); ?>

<div class="overlay" style="display: none;"></div>
<div class="loading-img" style="display: none"></div>

<div class="row mt-10 mb-20">
    <div class="col-sm-6 col-tab-6 col-xs-12 mb-10">

        <?= 'Showing ' . $startItem . ' - ' . $endItem . ' of ' . $totalCount . ' results'; ?>

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

<div class="row">
    <div class="col-md-12">
        <ul class="works-grid works-grid-gut works-grid-4" id="photo-gallery">

            <?php
            if (!empty($modelUserPostMain)):

                foreach ($modelUserPostMain as $dataUserPostMain): ?>

                    <li class="work-item">
                        <div class="gallery-item place-gallery">
                            <div class="gallery-image">
                                <div class="work-image">

                                    <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $dataUserPostMain['image'], 200, 200), ['class' => 'img-component', 'data-id' => $dataUserPostMain['id']]) ?>

                                </div>
                                <div class="work-caption">
                                    <div class="work-descr photo-caption hidden-xs"><?= !empty($dataUserPostMain['text']) ? $dataUserPostMain['text'] : '' ?></div>
                                    <div class="work-descr">
                                        <a class="btn btn-d btn-small btn-xs btn-circle show-image" href="<?= Yii::getAlias('@uploadsUrl') . '/img/user_post/' . $dataUserPostMain['image'] ?>"><i class="fa fa-search"></i></a>
                                        <a class="btn btn-d btn-small btn-xs btn-circle share-image-<?= $dataUserPostMain['id'] ?>-trigger"><i class="fa fa-share-alt"></i></a>
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

        <?= 'Showing ' . $startItem . ' - ' . $endItem . ' of ' . $totalCount . ' results'; ?>

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
$jscript = '
    $("#pjax-photo-container").on("pjax:send", function() {
        $("#photo-gallery").parent().parent().siblings(".overlay").show();
        $("#photo-gallery").parent().parent().siblings(".loading-img").show();
    });

    $("#pjax-photo-container").on("pjax:complete", function() {
        $("#photo-gallery").parent().parent().siblings(".overlay").hide();
        $("#photo-gallery").parent().parent().siblings(".loading-img").hide();
    });

    $("#pjax-photo-container").on("pjax:error", function (event) {
        event.preventDefault();
    });

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

    $("#photo-gallery").find(".work-item").each(function() {

        var thisObj = $(this);
        var photoId = $(this).find(".work-image").children().data("id");

        $(this).find(".share-image-" + photoId + "-trigger").on("click", function() {

            var businessName = $(".business-name").text().trim();
            var url = window.location.href;

                url = url.replace("detail", "photo").replace($("#business_id").val(), photoId);
            var title = "Foto untuk " + businessName;
            var description = thisObj.find(".photo-caption").text();
            var image = window.location.protocol + "//" + window.location.hostname + thisObj.find(".work-image").children().attr("src");

            FB.ui({
                method: "share_open_graph",
                action_type: "og.likes",
                action_properties: JSON.stringify({
                        object: {
                            "og:url": url,
                            "og:title": title,
                            "og:description": description,
                            "og:image": image
                        }
                })
            },
            function (response) {
                if (response && !response.error_message) {

                    messageResponse("aicon aicon-icon-tick-in-circle", "Sukses.", "Foto berhasil di posting ke Facebook Anda.", "success");
                }
            });
        });
    });

    $(".total-photo").html("' . $totalCount . '");
';

$this->registerJs($jscript);

Pjax::end(); ?>