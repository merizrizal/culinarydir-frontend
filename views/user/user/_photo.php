<?php 
/* @var $this yii\web\View */
/* @var $username string */ ?>

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
            data: {
                "username": "' . $username . '"
            },
            url: "' . Yii::$app->urlManager->createUrl(['user-data/user-post-photo']) . '",
            success: function(response) {

                $(".user-post-photo").html(response);
                
                $(".delete-image").on("click", function() {

                    $("#modal-confirmation").modal("show");
                    
                    $("#modal-confirmation").find(".modal-body").html("' . Yii::t('app', 'Are you sure want to delete this photo?') . '");
                    $("#modal-confirmation").find("#btn-delete").data("href", $(this).attr("href"));
                    
                    $("#modal-confirmation").find("#btn-delete").off("click");
                    $("#modal-confirmation").find("#btn-delete").on("click", function() {
            
                        $.ajax({
                            cache: false,
                            type: "POST",
                            url: $(this).data("href"),
                            beforeSend: function(xhr) {
                
                                $(".user-post-photo").siblings(".overlay").show();
                                $(".user-post-photo").siblings(".loading-img").show();
                            },
                            success: function(response) {
                
                                $("#modal-confirmation").modal("hide");
                
                                if (response.success) {
                
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
            
                    return false;
                });
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostPhoto();
';

$this->registerJs($jscript); ?>