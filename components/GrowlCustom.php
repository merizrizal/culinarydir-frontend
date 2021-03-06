<?php

namespace frontend\components;

use yii\base\Widget;

class GrowlCustom extends Widget {

    public function init() {
        parent::init();

        \kartik\growl\GrowlAsset::register($this->getView());

        $jscript = '
            var messageResponse = function(icon, title, message, type) {
            
                $.notify({
                    icon: icon,
                    title: title,
                    message: message,
                },{
                    element: "body",
                    position: null,
                    type: type,
                    allow_dismiss: true,
                    newest_on_top: false,
                    showProgressbar: false,
                    placement: {
                        from: "bottom",
                        align: "left"
                    },
                    offset: 20,
                    spacing: 10,
                    z_index: 1031,
                    delay: 5000,
                    timer: 1000,
                    animate: {
                        enter: "animated fadeInDown",
                        exit: "animated fadeOutUp"
                    },
                    template:
                        "<div data-notify=\"container\" class=\"col-xs-11 col-sm-3 alert alert-{0}\" role=\"alert\">" +
                            "<button type=\"button\" aria-hidden=\"true\" class=\"close\" data-notify=\"dismiss\">×</button>" +
                            "<span data-notify=\"icon\"></span> " +
                            "<span data-notify=\"title\"><b>{1}</b></span><br>" +
                            "<span data-notify=\"message\">{2}</span>" +
                            "<div class=\"progress\" data-notify=\"progressbar\">" +
                                "<div class=\"progress-bar progress-bar-{0}\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\"></div>" +
                            "</div>" +
                        "</div>"
                });
            }
        ';

        $this->getView()->registerJs($jscript);
    }
}
