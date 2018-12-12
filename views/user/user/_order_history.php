<?php

/* @var $this yii\web\View */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-content">
            	<div class="order-history"></div>
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
    $.ajax({
        cache: false,
        type: "GET",
        url: "' . Yii::$app->urlManager->createUrl(['user-data/order-history']) . '",
        success: function(response) {

            $(".order-history").html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {

            messageResponse("aicon aicon-icon-info", xhr.status, xhr.responseText, "danger");
        }
    });
';

$this->registerJs($jscript); ?>