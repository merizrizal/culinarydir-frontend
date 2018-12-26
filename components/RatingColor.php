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

                    var elementContainer = $(this).find(containerRating);

                    if (elementContainer.hasClass("label-info")) {

                        elementContainer.removeClass("label-info");
                    } else if (elementContainer.hasClass("label-success")) {
                    
                        elementContainer.removeClass("label-success");
                    } else if (elementContainer.hasClass("label-gold")) {
                    
                        elementContainer.removeClass("label-gold");
                    } else if (elementContainer.hasClass("label-warning")) {
                    
                        elementContainer.removeClass("label-warning");
                    } else if (elementContainer.hasClass("label-danger")) {
                    
                        elementContainer.removeClass("label-danger");
                    } else if (elementContainer.hasClass("label-default")) {
                    
                        elementContainer.removeClass("label-default");
                    }

                    if (vote_value == 5) {

                        elementContainer.addClass("label-info");
                    } else if (vote_value < 5 && vote_value >= 4 ) {

                        elementContainer.addClass("label-success");
                    } else if (vote_value < 4 && vote_value >= 3 ) {

                        elementContainer.addClass("label-gold");
                    } else if (vote_value < 3 && vote_value >= 2 ) {

                        elementContainer.addClass("label-warning");
                    } else if (vote_value < 2 && vote_value >= 1 ) {

                        elementContainer.addClass("label-danger");
                    } else {

                        elementContainer.addClass("label-default");
                    }
                });
            }
        ';

        $this->getView()->registerJs($jscript, View::POS_HEAD);
    }
}
