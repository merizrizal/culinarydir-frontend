<?php

namespace frontend\components;

use yii\base\Widget;

class FacebookShare extends Widget {

    public function init() {
        parent::init();

        $jscript = '
            var facebookShare = function(params) {

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

                        messageResponse("aicon aicon-icon-tick-in-circle", "Sukses.", params.type + " berhasil di posting ke Facebook Anda.", "success");
                    }
                });
            }
        ';

        $this->getView()->registerJs($jscript);
    }
}