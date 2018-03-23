<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\cloudformation\controllers;


use flipboxlabs\evo\modules\cloudformation\actions\Build;
use flipboxlabs\evo\modules\cloudformation\actions\ListTemplates;
use yii\console\Controller;

class DefaultController extends Controller
{
    public $defaultAction = 'list';

    public function actions()
    {
        return [
            'list'  => ListTemplates::class,
            'build' => Build::class,
        ];

    }
}