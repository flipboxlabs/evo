<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 8:41 PM
 */

namespace flipboxlabs\evo\modules\docker\controllers;

use flipboxlabs\evo\modules\docker\actions\Apache;
use flipboxlabs\evo\modules\docker\actions\Nginx;
use yii\console\Controller;

class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'apache' => Apache::class,
            'nginx' => Nginx::class,
        ];
    }
}