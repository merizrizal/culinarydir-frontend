<?php

namespace frontend\components;

use yii\base\Widget;
use yii\web\View;

class RatingColor extends Widget {

    public function init() {
        parent::init();

        $jscript = '
            function ratingColor(element, containerRating) {

                element.each(function() {

                    var vote_value = parseFloat($(this).find(containerRating).html());

                    if (vote_value == 5) {

                        $(this).find(containerRating).removeClass().addClass("label label-info pt-10");
                    } else if (vote_value < 5 && vote_value >= 4 ) {

                        $(this).find(containerRating).removeClass().addClass("label label-success pt-10");
                    } else if (vote_value < 4 && vote_value >= 3 ) {

                        $(this).find(containerRating).removeClass().addClass("label label-gold pt-10");
                    } else if (vote_value < 3 && vote_value >= 2 ) {

                        $(this).find(containerRating).removeClass().addClass("label label-warning pt-10");
                    } else if (vote_value < 2 && vote_value >= 1 ) {

                        $(this).find(containerRating).removeClass().addClass("label label-danger pt-10");
                    } else {

                        $(this).find(containerRating).removeClass().addClass("label label-default pt-10");
                    }
                });
            }
        ';

        $this->getView()->registerJs($jscript, View::POS_HEAD);
    }
}
