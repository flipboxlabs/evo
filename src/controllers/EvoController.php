<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/19/17
 * Time: 4:06 PM
 */

namespace flipboxlabs\evo\controllers;

use flipboxlabs\evo\Evo;
use flipboxlabs\evo\models\Environment;
use flipboxlabs\evo\models\EvoConfig;
use Symfony\Component\Yaml\Yaml;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class EvoController extends Controller
{
    const DEFAULT_CONFIG_LOCATION = APP_ROOT . '/.evo/config';
    const TEMPLATES = EVO_ROOT . '/evo-templates';
    const DOCKER_TEMPLATES = self::TEMPLATES . '/docker-compose';

    public function actionIndex($environmentName)
    {

        $location = $this->prompt(
            $this->ansiFormat('Set default config location.', Console::FG_YELLOW),
            [
                'default' => static::DEFAULT_CONFIG_LOCATION,
            ]
        );

        if (file_exists($location)) {
            $this->stdout('File exists ... reading file.');
            $config = new EvoConfig($location);
        } else {
            $config = new EvoConfig();
        }

        if (! $environment = $config->getEnvironment($environmentName)) {
            $config->addEnvironment(
                $environment = new Environment($environmentName)
            );
        }


        $this->stdout('Set the following variables for this environment:' . PHP_EOL, Console::FG_YELLOW);
        foreach ($environment->getAttributes() as $key => $attribute) {
            $environment->{$key} = $this->prompt(
                $this->ansiFormat($key, Console::FG_YELLOW), [
                'default' => $attribute,
            ]);
        }

        $this->stdout('File looks like this and will be saved here: ' . $location . PHP_EOL, Console::FG_YELLOW);
        $this->stdout(Yaml::dump($config->toArray(), 4) . PHP_EOL, Console::FG_GREEN);

        if (! $this->confirm(
            $this->ansiFormat('Do you want to save this environment?', Console::FG_YELLOW),
            true
        )) {
            $this->stdout('Not saving ... exiting.' . PHP_EOL, Console::FG_RED);
            return ExitCode::OK;
        }
        if (! Evo::getInstance()->getConfig()->save($config)) {
            $this->stdout('Config save error! Was not able to save config!', Console::FG_RED);
            return ExitCode::IOERR;
        }

        $this->stdout('Environment saved!', Console::FG_GREEN);
        return ExitCode::OK;
    }

}