<?php
namespace frontend\components;

use yii\base\Widget;
use yii\web\View;

class FacebookShare extends Widget
{
    public function init()
    {
        parent::init();

        $this->getView()->registerJs('
            function facebookShare(params) {

                FB.ui({
                    method: "share_open_graph",
                    action_type: "og.likes",
                    action_properties: JSON.stringify({
                        object: {
                            "og:url": params.ogUrl,
                            "og:title": params.ogTitle,
                            "og:description": params.ogDescription,
                            "og:image": params.ogImage
                        }
                    })
                },
                function (response) {
                    if (response && !response.error_message) {

                        messageResponse("aicon aicon-icon-tick-in-circle", "Sukses.", params.type + " berhasil diposting ke Facebook Anda.", "success");
                    }
                });
            }
        ', View::POS_HEAD);

        $this->getView()->registerJs('
            window.fbAsyncInit = function() {
                FB.init({
                    appId            : "' . \Yii::$app->params['facebook']['clientId'] . '",
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : "v3.1"
                });
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, "script", "facebook-jssdk"));
        ', View::POS_END);
    }
}