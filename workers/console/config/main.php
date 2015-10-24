<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'console\log\WorkerFileTarget',
                    'levels' => ['info'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'except' => ['worker'],
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
