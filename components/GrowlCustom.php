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
                    offset: 14,
                    spacing: 10,
                    z_index: 1031,
                    delay: 5000,
                    timer: 1000,
                    animate: {
                        enter: "animated fadeInDown",
                        exit: "animated fadeOutUp"
                    },
                    template:
                        "<div data-notify=\"container\" class=\"col-xs-11 col-sm-5 col-lg-3 alert alert-{0}\" role=\"alert\">" +
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
    }
    
    public static function stickyResponse()
    {
        return '
            function stickyGrowl(icon, title, message, type) {
            
                return $.notify({
                        icon: icon,
                        title: title,
                        message: message,
                        url: "' . Yii::$app->urlManager->createUrl(['order/order-list']) . '"
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
                        offset: 14,
                        spacing: 10,
                        z_index: 1031,
                        delay: 0,
                        animate: {
                            enter: "animated fadeInDown",
                            exit: "animated fadeOutUp"
                        },
                        template:
                            "<div data-notify=\"container\" class=\"col-xs-11 col-sm-4 col-md-3 alert alert-{0}\" role=\"alert\">" +
                                "<button type=\"button\" aria-hidden=\"true\" class=\"close\" data-notify=\"dismiss\">×</button>" +
                                "<span data-notify=\"icon\"></span> " +
                                "<span data-notify=\"title\"><b>{1}</b></span><br>" +
                                "<span data-notify=\"message\">{2}</span>" +
                                "<div class=\"progress\" data-notify=\"progressbar\">" +
                                    "<div class=\"progress-bar progress-bar-{0}\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\"></div>" +
                                "</div>" +
                                "<a href=\"{3}\" data-notify=\"url\"></a>" +
                            "</div>"
                    });
            }
        ';
    }
}
