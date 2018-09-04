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
    $("#modal-confirmation").find("#btn-delete").on("click", function() {

        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("data-href"),
            beforeSend: function(xhr) {
                $(".user-post-photo").siblings(".overlay").show();
                $(".user-post-photo").siblings(".loading-img").show();
            },
            success: function(response) {

                $("#modal-confirmation").modal("hide");

                if (response.status == "sukses") {

                    getUserPostPhoto();

                    messageResponse(response.icon, response.title, response.message, response.type);
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }

                $(".user-post-photo").siblings(".overlay").hide();
                $(".user-post-photo").siblings(".loading-img").hide();
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    });

    function getUserPostPhoto() {
        $.ajax({
            cache: false,
            type: "GET",
            data: {
                "username": "' . $username . '"
            },
            url: "' . Yii::$app->urlManager->createUrl(['user-data/get-user-post-photo']) . '",
            success: function(response) {

               $(".user-post-photo").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostPhoto();
';

$this->registerJs($jscript); ?>