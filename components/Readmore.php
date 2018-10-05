<?php

namespace frontend\components;

use yii\base\Widget;
use yii\web\View;

class Readmore extends Widget {

    public function init() {
        parent::init();

        $jscript = '
            var readmoreText = function(options) {

                var minChars = options.minChars;
                var ellipsesText = options.ellipsesText;
                var moreText = options.moreText;
                var lessText = options.lessText;

                options.element.each(function() {

                    var content = $(this).html();

                    if(content.length > minChars) {

                        var shownContent = content.substr(0, minChars);
                        var hiddenContent = content.substr(minChars, content.length - minChars);

                        var html = shownContent + "<span class=\"readmore-ellipses\">" + ellipsesText + "</span><span class=\"readmore-content\"><span>" + hiddenContent + "</span><a href=\"#\" class=\"text-main readmore-trigger\">" + moreText + "</a></span>";

                        $(this).html(html);
                    }

                });

                options.element.find(".readmore-trigger").click(function(){

                    if($(this).hasClass("less")) {

                        $(this).removeClass("less");
                        $(this).html(moreText);
                    } else {

                        $(this).addClass("less");
                        $(this).html(lessText);
                    }

                    $(this).parent().prev().toggle();
                    $(this).prev().toggle();

                    return false;
                });
            }
        ';

        $this->getView()->registerJs($jscript, View::POS_HEAD);
    }
}

