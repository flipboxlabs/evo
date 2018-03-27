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
use flipboxlabs\evo\constants\Evo as EvoConstants;
use Symfony\Component\Yaml\Yaml;
use yii\base\Model;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class EvoController extends Controller
{
    protected function loopAndSetAttributes(Model $model)
    {
        foreach ($model->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                continue;
            }
            $model->{$key} = $this->prompt(
                $this->ansiFormat($model->getAttributeLabel($key), Console::FG_YELLOW), [
                'default' => $attribute,
            ]);

        }

    }

    /**
     * @param $environmentName
     * @return int
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function actionIndex($environmentName)
    {

        $location = $this->prompt(
            $this->ansiFormat('Set default config location.', Console::FG_YELLOW),
            [
                'default' => EvoConstants::EVO_CONFIG_FILE,
            ]
        );

        if (file_exists($location)) {
            $this->stdout('File exists ... reading file.' . PHP_EOL, Console::FG_CYAN);
            $config = new EvoConfig($location);
        } else {
            $config = new EvoConfig();
        }

        if (! $environment = $config->getEnvironment($environmentName)) {
            $config->addEnvironment(
                $environment = new Environment($environmentName)
            );
        }

        $this->stdout('Set the following variables for this project:' . PHP_EOL, Console::FG_CYAN);
        $this->loopAndSetAttributes($config);

        $this->stdout('Set the following variables for this environment:' . PHP_EOL, Console::FG_CYAN);
        $this->loopAndSetAttributes($environment);

        $this->stdout('File looks like this and will be saved here: ' . $location . PHP_EOL, Console::FG_CYAN);
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


    /**
     * Dumps the .evo/config file to stdout
     * @return int
     */
    public function actionCat()
    {
        if(!Evo::getInstance()->getConfig()->configExists()){
            $this->stderr('Could not find config!'. PHP_EOL, Console::FG_RED);
            $this->stdout('Create your config by running: '.PHP_EOL, Console::FG_YELLOW);
            $this->stdout('evo config <environmentName>'.PHP_EOL, Console::FG_GREEN);
            return ExitCode::CONFIG;
        }

        $this->stdout(
            Evo::getInstance()->getConfig()->getContents() . PHP_EOL,
            Console::FG_CYAN
        );

        return ExitCode::OK;

    }

}