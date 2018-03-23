<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\cloudformation\actions;


use flipboxlabs\evo\constants\CloudformationTemplates;
use yii\base\Action;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class ListTemplates extends Action
{

    public function run()
    {
        foreach (CloudformationTemplates::all() as $template) {

            $this->controller->stdout(basename($template) . PHP_EOL, Console::FG_CYAN);
        }

        return ExitCode::OK;
    }
}