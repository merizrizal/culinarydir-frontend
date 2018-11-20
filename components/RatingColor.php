<?php
namespace frontend\components;

use yii\base\Widget;
use yii\web\View;

class RatingColor extends Widget
{

    public function init()
    {
        parent::init();

        $jscript = '
            function ratingColor(element, containerRating) {

                element.each(function() {

                    var vote_value = parseFloat($(this).find(containerRating).html());

                    if (vote_value == 5) {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-info");
                    } else if (vote_value < 5 && vote_value >= 4 ) {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-success");
                    } else if (vote_value < 4 && vote_value >= 3 ) {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-gold");
                    } else if (vote_value < 3 && vote_value >= 2 ) {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-warning");
                    } else if (vote_value < 2 && vote_value >= 1 ) {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-danger");
                    } else {

                        $(this).find(containerRating).removeClass("label-success").addClass("label-default");
                    }
                });
            }
        ';

        $this->getView()->registerJs($jscript, View::POS_HEAD);
    }
}
