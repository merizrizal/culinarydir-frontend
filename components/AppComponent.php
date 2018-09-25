<?php

namespace frontend\components;

use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AppComponent extends Widget {

    public $showFacilityFilter = false;

    public function header() {

        return $this->render('app-component/app_header', array(

        ));
    }

    public function navigation() {
        return $this->render('app-component/app_navigation', array(

        ));
    }

    public function appFooter() {
        return $this->render('app-component/app_footer', array(

        ));
    }

    public function search($config = []) {
        
        return $this->render('app-component/search', ArrayHelper::merge($config, [
            'showFacilityFilter' => $this->showFacilityFilter,
        ]));
    }

    public function searchPopover($config = []) {
        return $this->render('app-component/search_popover', ArrayHelper::merge($config, array()));
    }

    public function searchJsComponent() {
        return $this->render('app-component/_search_js_component', array(

        ));
    }

}
