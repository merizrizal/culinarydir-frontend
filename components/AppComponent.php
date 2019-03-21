<?php

namespace frontend\components;

use yii\base\Widget;

class AppComponent extends Widget
{

    public $showFacilityFilter = false;

    public function header()
    {
        return $this->render('app-component/app_header', [
            
        ]);
    }

    public function navigation()
    {
        return $this->render('app-component/app_navigation', [
            
        ]);
    }

    public function appFooter()
    {
        return $this->render('app-component/app_footer', [
            
        ]);
    }

    public function search($config = [])
    {
        return $this->render('app-component/search', $config);
    }

    public function searchPopover($config = [])
    {
        return $this->render('app-component/search_popover', $config);
    }

    public function searchJsComponent($keyword, $pageType = null)
    {
        return $this->render('app-component/_search_js_component', [
            'keyword' => $keyword,
            'pageType' => empty($pageType) ? 'list' : $pageType,
            'showFacilityFilter' => $this->showFacilityFilter
        ]);
    }

}
