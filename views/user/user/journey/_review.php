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
            },
            error: function(xhr, ajaxOptions, thrownError) {
    
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostReview();

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

        var thisObj = $(this);
        
        thisObj.parents(".user-post-item").find(".user-comment-review").slideToggle();
        thisObj.parents(".user-post-item").find(".input-comments-review").trigger("focus");            

        return false;
    });

    $(".user-post-section").on("keypress", ".input-comments-review", function(event) {

        var thisObj = $(this);

        if (event.which == 13 && $(this).val().trim()) {

            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    "user_post_main_id": thisObj.parents(".user-post-item").find(".user-post-main-id").val(),
                    "text": thisObj.val(),
                },
                url: "' . Yii::$app->urlManager->createUrl(['action/submit-comment']) . '",
                beforeSend: function(xhr) {
                    
                    thisObj.parent().siblings(".overlay").show();
                    thisObj.parent().siblings(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {

                        thisObj.val("");

                        $.ajax({
                            cache: false,
                            type: "POST",
                            data: {
                                "user_post_main_id": response.user_post_main_id
                            },
                            url: "' . Yii::$app->urlManager->createUrl(['data/post-comment']) . '",
                            success: function(response) {
                                
                                thisObj.parent().siblings(".comment-section").html(response);

                                thisObj.parents(".user-post-item").find("span.total-comments-review").html(commentCount);
                            },
                            error: function(xhr, ajaxOptions, thrownError) {

                                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
                            }
                        });
                    } else {

                        messageResponse(response.icon, response.title, response.message, response.type);
                    }

                    thisObj.parent().siblings(".overlay").hide();
                    thisObj.parent().siblings(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                    thisObj.parent().siblings(".overlay").hide();
                    thisObj.parent().siblings(".loading-img").hide();
                }
            });
        }
    });
    
    $(".user-post-section").on("click", ".user-photos-review-trigger", function() {

        var thisObj = $(this);

        if (thisObj.parents(".user-post-item").find(".user-photo-review").find(".gallery-photo-review").length) {       

            thisObj.parents(".user-post-item").find(".user-photo-review").toggle(500);
        }
        
        return false;
    });
    
    $(".user-post-section").on("click", ".share-review-trigger", function() {

        var thisObj = $(this);

        var url = "' . Yii::$app->urlManager->createAbsoluteUrl(['page/review']) . '/" + thisObj.parents(".user-post-item").find(".user-post-main-id").val();
        var title = "Rating " + thisObj.parents(".user-post-item").find(".rating").text().trim() + " untuk " + thisObj.parents(".user-post-item").find(".business-name").val();
        var description = thisObj.parents(".user-post-item").find(".review-description").text();
        var image = window.location.protocol + "//" + window.location.hostname + thisObj.parents(".user-post-item").find(".user-photo-review").eq(0).find(".work-image").children().attr("src");

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

        var thisObj = $(this);

        $("#modal-confirmation").find("#btn-delete").data("href", thisObj.attr("href"));
        
        $("#modal-confirmation").find("#btn-delete").off("click");
        $("#modal-confirmation").find("#btn-delete").on("click", function() {
            
            $.ajax({
                cache: false,
                type: "POST",
                url: $(this).data("href"),
                beforeSend: function(xhr) {
                    
                    $(".user-post-section").find(".overlay").show();
                    $(".user-post-section").find(".loading-img").show();
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

                    $(".user-post-section").find(".overlay").hide();
                    $(".user-post-section").find(".loading-img").hide();
                },
                error: function(xhr, ajaxOptions, thrownError) {

                    messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");

                    $("#modal-confirmation").modal("hide");
    
                    $(".user-post-section").find(".overlay").hide();
                    $(".user-post-section").find(".loading-img").hide();
                }
            });
        });

        $("#modal-confirmation").modal("show");

        return false;
    });
';

$this->registerJs($jscript); ?>