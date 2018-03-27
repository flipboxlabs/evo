<?php

return [
    'id'                  => 'Evo',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'flipboxlabs\\evo\\controllers',
    'aliases'             => [
        '@flipboxlabs/evo' => dirname(__DIR__, 2),
    ],
    'controllerMap'       => [
        'help'           => \flipboxlabs\evo\controllers\HelpController::class,
        'cloudformation' => \flipboxlabs\evo\modules\cloudformation\controllers\DefaultController::class,
        'config'         => \flipboxlabs\evo\controllers\EvoController::class,
        'params'         => \flipboxlabs\evo\modules\parameter\controllers\ParamController::class,
        'docker'         => \flipboxlabs\evo\modules\docker\controllers\DefaultController::class,
    ],
    'modules'             => [
        'aws'            => \flipboxlabs\evo\modules\aws\Aws::class,
        'cloudformation' => \flipboxlabs\evo\modules\cloudformation\Cloudformation::class,
        'docker'         => \flipboxlabs\evo\modules\docker\Docker::class,
        'parameter'      => \flipboxlabs\evo\modules\parameter\Parameter::class,
        'webserver'      => \flipboxlabs\evo\modules\webserver\WebServer::class,
    ],
    'components'          => [
        'config' => \flipboxlabs\evo\services\ConfigService::class,
        'twig'   => \flipboxlabs\evo\services\Twig::class,
    ]
];