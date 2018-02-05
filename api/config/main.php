<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'defaultRoute'=>'index/index',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'parsers'=>[
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response'=>[
            'class'=>'yii\web\Response',
            'format'=>yii\web\Response::FORMAT_JSON,
            'charset'=>'UTF-8',
            'on beforeSend'=>function($event) {
                $response = $event->sender;
                if ( !$response->isSuccessful) {
                    if ($response->data !== null) {
                        $response->statusCode = $response->data['status'];
                        $response->data = [
                            'code' => $response->data['code'],
                            'msg' => $response->data['message'],
                            'data'=>[],
                        ];
                    }
                }
            }
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => false,
            'enableSession'=>false, // 禁用session
            'loginUrl'=>null,
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
//            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
