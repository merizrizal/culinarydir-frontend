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
    'container' => [
        'definitions' => [
            'yii\data\Pagination' => [
                'pageSize' => 21
            ]
        ],
    ],
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
        'authClientCollection' => [
            'class' => yii\authclient\Collection::class,
            'clients' => [
                'google' => [
                    'class' => yii\authclient\clients\Google::class,
                    'clientId' => $params['google']['clientId'],
                    'clientSecret' => $params['google']['clientSecret'],
                ],
                'facebook' => [
                    'class' => yii\authclient\clients\Facebook::class,
                    'clientId' => $params['facebook']['clientId'],
                    'clientSecret' => $params['facebook']['clientSecret'],
                    'attributeNames' => ['name', 'email', 'first_name', 'last_name', 'address'],
                ],
            ],
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
                'kuliner/di/<city:\w+(-\w+)*>/<uniqueName:\w+(-\w+)*>' => 'page/detail',
                
                '<searchType:\w+(-\w+)*>/kuliner/di/<city:\w+(-\w+)*>' => 'page/result-list',
                '<searchType:\w+(-\w+)*>/data-kuliner/di/<city:\w+(-\w+)*>' => 'data/result-list',
                
                'map/<searchType:\w+(-\w+)*>/kuliner/di/<city:\w+(-\w+)*>' => 'page/result-map',
                'map/<searchType:\w+(-\w+)*>/data-kuliner/di/<city:\w+(-\w+)*>' => 'data/result-map',
                
                '' => 'page/index',
                
                '<controller:\w+>/<action:\w+>/<id:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]
        ],
    ],
    'params' => $params,
];
