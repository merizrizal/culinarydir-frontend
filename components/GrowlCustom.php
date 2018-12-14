<?php
namespace frontend\components;

use Yii;
use yii\base\Widget;

class GrowlCustom extends Widget
{

    public function init()
    {
        parent::init();

        \kartik\growl\GrowlAsset::register($this->getView());
    }
    
    public static function messageResponse()
    {
        return '
            function messageResponse(icon, title, message, type) {
            
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
                    offset: {
                        x: 0,
                        y: ' . (Yii::$app->request->getUserAgent() == 'com.asikmakan.app' ? 60 : 0) . '
                    },
                    spacing: 0,
                    z_index: 1031,
                    delay: 5000,
                    timer: 1000,
                    animate: {
                        enter: "animated fadeInDown",
                        exit: "animated fadeOutUp"
                    },
                    template:
                        "<div data-notify=\"container\" class=\"col-lg-3 col-md-4 col-sm-5 col-xs-12\">" +
                            "<div class=\"alert alert-{0} mb-10\" style=\"\" role=\"alert\">" +
                                "<button type=\"button\" aria-hidden=\"true\" class=\"close\" data-notify=\"dismiss\">×</button>" +
                                "<span data-notify=\"icon\"></span> " +
                                "<span data-notify=\"title\"><b>{1}</b></span><br>" +
                                "<span data-notify=\"message\">{2}</span>" +
                                "<div class=\"progress\" data-notify=\"progressbar\">" +
                                    "<div class=\"progress-bar progress-bar-{0}\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\"></div>" +
                                "</div>" +
                            "</div>" +
                        "</div>"
                });
            }
        ';
    }
    
    public static function stickyResponse()
    {
        return '
            function stickyGrowl(icon, title, message, type) {
            
                return $.notify({
                        icon: icon,
                        title: title,
                        message: message,
                        url: "' . Yii::$app->urlManager->createUrl(['order/checkout']) . '"
                    },{
                        element: "body",
                        position: null,
                        type: type,
                        allow_dismiss: false,
                        newest_on_top: false,
                        showProgressbar: false,
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                        offset: {
                            x: 0,
                            y: ' . (Yii::$app->request->getUserAgent() == 'com.asikmakan.app' ? 60 : 0) . '
                        },
                        spacing: 0,
                        z_index: 1031,
                        delay: 0,
                        animate: {
                            enter: "animated fadeInDown",
                            exit: "animated fadeOutUp"
                        },
                        template:
                            "<div data-notify=\"container\" class=\"col-lg-3 col-md-4 col-sm-5 col-xs-12\">" +
                                "<div class=\"alert alert-{0} mb-10\" style=\"\" role=\"alert\">" +
                                    "<button type=\"button\" aria-hidden=\"true\" class=\"close\" data-notify=\"dismiss\">×</button>" +
                                    "<span data-notify=\"icon\"></span> " +
                                    "<span data-notify=\"title\"><b>{1}</b></span><br>" +
                                    "<span data-notify=\"message\">{2}</span>" +
                                    "<div class=\"progress\" data-notify=\"progressbar\">" +
                                        "<div class=\"progress-bar progress-bar-{0}\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\"></div>" +
                                    "</div>" +
                                "</div>" +
                                "<a href=\"{3}\" data-notify=\"url\"></a>" +
                            "</div>"
                    });
            }
        ';
    }
}
