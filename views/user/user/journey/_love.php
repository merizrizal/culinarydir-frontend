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
        data: {
            "username": "' . $username . '"
        },
        url: "' . Yii::$app->urlManager->createUrl(['user-data/get-user-love']) . '",
        success: function(response) {

            $(".user-love-section").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });
';

$this->registerJs($jscript); ?>