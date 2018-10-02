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
';

$this->registerJs($jscript); ?>