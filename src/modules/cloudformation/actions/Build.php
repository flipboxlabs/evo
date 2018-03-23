<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\cloudformation\actions;


use flipboxlabs\evo\constants\CloudformationTemplates;
use Symfony\Component\Yaml\Yaml;
use yii\base\Action;
use yii\console\ExitCode;
use yii\helpers\Console;

class Build extends Action
{

    /**
     * @param $file
     * Filename of the template that you want to build
     * Use list command to see available files
     * i.e., eb.yaml
     */
    public function run(string $file)
    {
        $fullpath = '';
        foreach (CloudformationTemplates::all() as $template) {
            if ($file === basename($template)) {
                $fullpath = $template;
            }
        }

        if ($fullpath === '') {
            $this->controller->stderr('File not found! ' . $file . PHP_EOL, Console::FG_RED);
            return ExitCode::CONFIG;
        }

        $parsed = Yaml::parse(file_get_contents($fullpath), Yaml::PARSE_CUSTOM_TAGS);

        if (isset($parsed['Parameters'])) {
            foreach ($parsed['Parameters'] as $name => $parameter) {
                $this->controller->prompt(
                    sprintf(
                        $this->controller->ansiFormat('[%s]', Console::FG_CYAN) .
                        $this->controller->ansiFormat(' %s', Console::FG_GREEN),
                        $name,
                        isset($parameter['Description']) ? $parameter['Description'] : ''), [
                        'default' => isset($parameter['Default']) ? $parameter['Default'] : '',
                    ]
                );
            }
        }
        return ExitCode::OK;
    }
}