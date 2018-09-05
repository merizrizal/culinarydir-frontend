<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/media';
    public $baseUrl = '@web/media';

    public $css = [
        'lib/asicon/asicon.css',
        'lib/animate.css/animate.css',
        'css/style.css',
        'css/colors/default.css',
    ];
    public $js = [
        'js/plugins.js',
        'js/main.js',
    ];
    public $depends = [
        'common\assets\AppAsset',
    ];
}
