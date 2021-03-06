<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'language' => 'id',
    'name' => 'Asikmakan',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'page/index',
    //'catchAll' => ['site/maintenance'], //dont delete, just comment to deactive
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-front-asikmakan-synctech',
        ],
        'user' => [
            'identityClass' => 'core\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-front-asikmakan-synctech', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'front-asikmakan',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<action:\w+>/<id:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]
        ],
    ],
    'params' => $params,
];
