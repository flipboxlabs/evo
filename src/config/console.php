<?php

return [
    'id'                  => 'Evo',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'flipboxlabs\\evo\\controllers',
    'aliases'             => [
        '@flipboxlabs/evo' => dirname(__DIR__, 2),
    ],
    'controllerMap'       => [
        'help'   => \flipboxlabs\evo\controllers\HelpController::class,
        'config' => \flipboxlabs\evo\controllers\EvoController::class,
        'dotenv' => \flipboxlabs\evo\controllers\ParamController::class,
        'docker' => \flipboxlabs\evo\modules\docker\controllers\DefaultController::class,
        'cloudformation' => \flipboxlabs\evo\modules\cloudformation\controllers\DefaultController::class,
    ],
    'modules'             => [
        'docker'         => \flipboxlabs\evo\modules\docker\Docker::class,
        'webserver'      => \flipboxlabs\evo\modules\webserver\WebServer::class,
        'cloudformation' => \flipboxlabs\evo\modules\cloudformation\Cloudformation::class,
    ],
    'components'          => [
        'config' => \flipboxlabs\evo\services\ConfigService::class,
        'twig'   => \flipboxlabs\evo\services\Twig::class,
    ]
];