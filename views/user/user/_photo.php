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

                $(".user-post-photo").magnificPopup({

                    delegate: ".place-gallery a.show-image",
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

                $(".user-post-photo").on("click", ".share-image-trigger", function() {

                    var rootObj = $(this).parents(".work-item");

                    var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/photo']) . '/" + rootObj.find(".work-image img").data("id");
                    var title = "Foto untuk " + rootObj.find(".business-name").val();
                    var description = rootObj.find(".photo-caption").text();
                    var image = window.location.protocol + "//" + window.location.hostname + rootObj.find(".work-image").children().attr("src");
        
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
                    
                    $("#modal-confirmation").find("#btn-delete").off("click");
                    $("#modal-confirmation").find("#btn-delete").on("click", function() {
            
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
            },
            error: function(xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostPhoto();
';

$this->registerJs($jscript); ?>