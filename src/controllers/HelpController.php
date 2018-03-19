<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/21/17
 * Time: 2:08 PM
 */

namespace flipboxlabs\evo\controllers;


class HelpController extends \yii\console\controllers\HelpController
{

    protected function getDefaultHelpHeader()
    {
        $ascii = <<<EOF
 ___
(_      
/__\/()

by https://flipboxfactory.com
EOF;

        return PHP_EOL . $ascii . PHP_EOL;
    }
}