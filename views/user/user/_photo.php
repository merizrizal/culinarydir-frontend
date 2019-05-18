<?php

/* @var $this yii\web\View */
/* @var $username string */
/* @var $queryParams array */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-content">
                <div class="user-post-photo"></div>
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
    function getUserPostPhoto() {

        $.ajax({
            cache: false,
            type: "GET",
            url: "' . \Yii::$app->urlManager->createUrl([
                'user-data/user-post-photo',
                'username' => $username
            ]). (!empty($queryParams['redirect']) && $queryParams['redirect'] == 'photo' ? '?page=' . $queryParams['page'] . '&per-page=' . $queryParams['per-page'] : '') . '",
            success: function(response) {

                $(".user-post-photo").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostPhoto();

    $(".user-post-photo").on("click", ".share-image-trigger", function() {

        var url = $(this).attr("href");
        var title = "Foto untuk " + $(this).parents(".work-item").find(".business-name").val();
        var description = $(this).parents(".work-item").find(".photo-caption").text() !== "" ? $(this).find(".photo-caption").text() : "Temukan Bisnis Kuliner Favorit Anda di Asikmakan.com";
        var image = window.location.protocol + "//" + window.location.hostname + $(this).parents(".work-item").find(".work-image img").attr("src");

        facebookShare({
            ogUrl: url,
            ogTitle: title,
            ogDescription: description,
            ogImage: image,
            type: "Foto"
        });

        return false;
    });

    $(".user-post-photo").on("click", ".delete-image", function() {

        $("#modal-confirmation").find("#btn-delete").data("href", $(this).attr("href"));

        $("#modal-confirmation").find("#btn-delete").on("click", function() {

            $("#modal-confirmation").find("#btn-delete").off("click");

            $.ajax({
                cache: false,
                type: "POST",
                url: $(this).data("href"),
                beforeSend: function(xhr) {

                    $(".user-post-photo-container").children(".overlay").show();
                    $(".user-post-photo-container").children(".loading-img").show();
                },
                success: function(response) {

                    $("#modal-confirmation").modal("hide");

                    if (response.success) {

                        getUserPostPhoto();

                        messageResponse(response.icon, response.title, response.message, response.type);
                    } else {

                        messageResponse(response.icon, response.title, response.message, response.type);
                    }

                    $(".user-post-photo-container").children(".overlay").hide();
                    $(".user-post-photo-container").children(".loading-img").hide();
                },
                error: function(xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                    $("#modal-confirmation").modal("hide");

                    $(".user-post-photo-container").children(".overlay").hide();
                    $(".user-post-photo-container").children(".loading-img").hide();
                }
            });
        });

        $("#modal-confirmation").modal("show");

        return false;
    });
';

$this->registerJs($jscript); ?>