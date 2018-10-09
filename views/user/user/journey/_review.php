<?php 
/* @var $this yii\web\View */
/* @var $username string */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="user-post-section"></div>
    </div>
</div>

<?php
$jscript = '
    function getUserPostReview() {

        $.ajax({
            cache: false,
            type: "GET",
            data: {
                "username": "' . $username . '"
            },
            url: "' . Yii::$app->urlManager->createUrl(['user-data/user-post']) . '",
            success: function(response) {
    
                $(".user-post-section").html(response);

                $(".user-post-section").on("click", ".user-likes-review-trigger", function() {

                    var thisObj = $(this);

                    $.ajax({
                        cache: false,
                        type: "POST",
                        data: {
                            "user_post_main_id": thisObj.parents(".user-post-item").find(".user-post-main-id").val()
                        },
                        url: $(this).attr("href"),
                        success: function(response) {
        
                            if (response.success) {
        
                                var loveValue = parseInt(thisObj.parent().find(".user-likes-review-trigger").find("span.total-likes-review").html());
        
                                if (response.is_active) {
        
                                    thisObj.parent().find(".user-likes-review-trigger").addClass("selected");
                                    thisObj.parent().find("span.total-likes-review").html(loveValue + 1);
                                } else {
        
                                    thisObj.parent().find(".user-likes-review-trigger").removeClass("selected");
                                    thisObj.parent().find("span.total-likes-review").html(loveValue - 1);
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

                $(".user-post-section").on("click", ".user-comments-review-trigger", function() {
            
                    $(".user-post-section").find("#user-comments-review").slideToggle();
                    $(".user-post-section").find("#input-comments-review").trigger("focus");            
        
                    return false;
                });

                $(".user-post-section").on("keypress", "#input-comments-review", function(event) {

                    var thisObj = $(this);
            
                    if (event.which == 13 && $(this).val().trim()) {
        
                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": thisObj.parents(".user-post-item").find(".user-post-main-id").val(),
                                "text": $(this).val(),
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                            beforeSend: function(xhr) {

                                $(".comment-section").siblings(".overlay").show();
                                $(".comment-section").siblings(".loading-img").show();
                            },
                            success: function(response) {
        
                                if (response.success) {
        
                                    $("#input-comments-review").val("");
        
                                    $.ajax({
                                        cache: false,
                                        type: "POST",
                                        data: {
                                            "user_post_main_id": response.user_post_main_id
                                        },
                                        url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                                        success: function(response) {
                                            
                                            $(".comment-section").html(response);
        
                                            thisObj.parents(".user-post-item").find("span.total-comments-review").html(commentCount);
                                        },
                                        error: function(xhr, ajaxOptions, thrownError) {
        
                                            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                                        }
                                    });
                                } else {
        
                                    messageResponse(response.icon, response.title, response.message, response.type);
                                }
        
                                $(".comment-section").siblings(".overlay").hide();
                                $(".comment-section").siblings(".loading-img").hide();
                            },
                            error: function (xhr, ajaxOptions, thrownError) {

                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                                $(".comment-section").siblings(".overlay").hide();
                                $(".comment-section").siblings(".loading-img").hide();
                            }
                        });
                    }
                });

                $(".user-post-section").on("click", ".user-photos-review-trigger", function() {
            
                    if ($(".user-post-section").find("#user-photos-review").find(".gallery-photo-review").length) {            
        
                        $(".user-post-section").find("#user-photos-review").toggle(500);
                    }
                    
                    return false;
                });

                $(".user-post-section").magnificPopup({
            
                    delegate: ".post-gallery a.show-image",
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

                $(".user-post-section").on("click", ".share-review-trigger", function() {
            
                    var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + $(".user-post-section").find(".user-post-main-id").val();
                    var title = "Rating " + $(".user-post-section").find(".rating").text().trim() + " untuk " + $(".user-post-section").find(".business-name").val();
                    var description = $(".user-post-section").find(".review-description").text();
                    var image = window.location.protocol + "//" + window.location.hostname + $(".user-post-section").parent().find("#user-photos-review").eq(0).find(".work-image").children().attr("src");
        
                    facebookShare({
                        ogUrl: url,
                        ogTitle: title,
                        ogDescription: description,
                        ogImage: image,
                        type: "Review"
                    });
        
                    return false;
                });

                $(".user-post-section").on("click", ".user-delete-review-trigger", function() {

                    $("#modal-confirmation").find("#btn-delete").data("href", $(this).attr("href"));
                    
                    $("#modal-confirmation").find("#btn-delete").off("click");
                    $("#modal-confirmation").find("#btn-delete").on("click", function() {
        
                        $.ajax({
                            cache: false,
                            type: "POST",
                            url: $(this).data("href"),
                            beforeSend: function(xhr) {
            
                                $(".user-post-container").children(".overlay").show();
                                $(".user-post-container").children(".loading-img").show();
                            },
                            success: function(response) {
        
                                $("#modal-confirmation").modal("hide");
                
                                if (response.success) {
        
                                    getUserPostReview();
                
                                    var totalUserPost = parseInt($(".total-user-post").html());
                                    $(".total-user-post").html(totalUserPost - 1);

                                    messageResponse(response.icon, response.title, response.message, response.type);
                                } else {
                
                                    messageResponse(response.icon, response.title, response.message, response.type);
                                }

                                $(".user-post-container").children(".overlay").hide();
                                $(".user-post-container").children(".loading-img").hide();
                            },
                            error: function(xhr, ajaxOptions, thrownError) {

                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                                $("#modal-confirmation").modal("hide");
                
                                $(".user-post-container").children(".overlay").hide();
                                $(".user-post-container").children(".loading-img").hide();
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

    getUserPostReview();
';

$this->registerJs($jscript); ?>