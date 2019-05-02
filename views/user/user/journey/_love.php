<?php

/* @var $this yii\web\View */
/* @var $username string */
/* @var $queryParams array */ ?>

<div class="row love-place">
    <div class="col-sm-12 col-xs-12">
        <div class="user-love-section"></div>
    </div>
</div>

<?php
$jscript = '
    $.ajax({
        cache: false,
        type: "GET",
        url: "' . \Yii::$app->urlManager->createUrl([
            'user-data/user-love',
            'username' => $username
        ]) . (!empty($queryParams['redirect']) && $queryParams['redirect'] == 'love' ? '?page=' . $queryParams['page'] . '&per-page=' . $queryParams['per-page'] : '') . '",
        success: function(response) {

            $(".user-love-section").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });

    $(".user-love-section").on("click", ".unlove-place", function() {

        var thisObj = $(this);

        $.ajax({
            cache: false,
            url: thisObj.attr("href"),
            type: "POST",
            data: {
                "business_id": thisObj.data("business-id")
            },
            success: function(response) {

                if (response.success) {

                    var count = parseInt($(".total-user-love").html());

                    if (response.is_active) {

                        thisObj.html("<h2 class=\"mt-0 mb-0 text-red fas fa-heart\"></h2>");
                        $(".total-user-love").html(count + 1);
                    } else {

                        thisObj.html("<h2 class=\"mt-0 mb-0 text-red far fa-heart\"></h2>");
                        $(".total-user-love").html(count - 1);
                    }
                } else {

                    messageResponse(response.icon, response.title, response.message, response.type);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
            }
        });

        return false;
    });
';

$this->registerJs($jscript); ?>