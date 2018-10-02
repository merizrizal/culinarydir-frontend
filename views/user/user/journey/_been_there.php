<?php 
/* @var $this yii\web\View */
/* @var $username string */ ?>

<div class="row been-there">
    <div class="col-sm-12 col-xs-12">
        <div class="user-visit-section"></div>
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
        url: "' . Yii::$app->urlManager->createUrl(['user-data/user-visit']) . '",
        success: function(response) {

            $(".user-visit-section").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });
';

$this->registerJs($jscript); ?>