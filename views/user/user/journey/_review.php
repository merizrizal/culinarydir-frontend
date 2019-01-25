<?php

/* @var $this yii\web\View */
/* @var $username string */
/* @var $queryParams array */ ?>

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
            url: "' . Yii::$app->urlManager->createUrl([
                'user-data/user-post',
                'username' => $username
            ]) . (!empty($queryParams['redirect']) && $queryParams['redirect'] == 'review' ? '?page=' . $queryParams['page'] . '&per-page=' . $queryParams['per-page'] : '') . '",
            success: function(response) {
    
                $(".user-post-section").html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
    
                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });
    }

    getUserPostReview();

    $(".user-post-section").on("click", ".user-rating-popover", function() {

        return false;
    });

    $(".user-post-section").on("click", ".user-likes-review-trigger", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "user_post_main_id": thisObj.parents(".user-post-item").find(".user-post-main-id").val()
            },
            url: thisObj.attr("href"),
            success: function(response) {

                if (response.success) {

                    var loveValue = parseInt(thisObj.parents(".user-post-item").find(".total-likes-review").html());

                    if (response.is_active) {

                        thisObj.addClass("selected");
                        thisObj.parents(".user-post-item").find(".total-likes-review").html(loveValue + 1);
                    } else {

                        thisObj.removeClass("selected");
                        thisObj.parents(".user-post-item").find(".total-likes-review").html(loveValue - 1);
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
        
        $(this).parents(".user-post-item").find(".user-comment-review").slideToggle();
        $(this).parents(".user-post-item").find(".input-comments-review").trigger("focus");

        return false;
    });

    $(".user-post-section").on("keypress", ".input-comments-review", function(event) {

        var thisObj = $(this);

        if (event.which == 13 && thisObj.val().trim()) {

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
    
    $(".user-post-section").on("click", ".share-review-trigger", function() {

        var url = $(this).attr("href");
        var title = "Rating " + $(this).parents(".user-post-item").find(".rating > h3").text().trim() + " untuk " + $(this).parents(".user-post-item").find(".business-review > a").text();
        var description = $(this).parents(".user-post-item").find(".review-description").text();
        var image = window.location.protocol + "//" + window.location.hostname + "' . Yii::getAlias('@uploadsUrl') . '/img/image-no-available.jpg' . '";
        
        var userPhotoList = $(this).parents(".user-post-item").find(".gallery-photo-review");

        if (userPhotoList.length) {
        
            image = window.location.protocol + "//" + window.location.hostname + userPhotoList.eq(0).find(".work-image").children().attr("src").replace("72x72", "");
        }

        facebookShare({
            ogUrl: url,
            ogTitle: title,
            ogDescription: description,
            ogImage: image,
            type: "Review"
        });

        $(this).parent().parent().siblings("a").dropdown("toggle");

        return false;
    });

    $(".user-post-section").on("click", ".user-delete-review-trigger", function() {

        var thisObj = $(this);

        $("#modal-confirmation").find("#btn-delete").data("href", thisObj.attr("href"));

        $("#modal-confirmation").find("#btn-delete").on("click", function() {

            $("#modal-confirmation").find("#btn-delete").off("click");
            
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

        $(this).parent().parent().siblings("a").dropdown("toggle");

        return false;
    });
';

$this->registerJs($jscript); ?>