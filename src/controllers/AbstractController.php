<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\controllers;


use flipboxlabs\evo\Evo;
use flipboxlabs\evo\models\Environment;
use flipboxlabs\evo\models\EvoConfig;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

abstract class AbstractController extends Controller
{

    /**
     * @var string
     * Environment identifier you'd like to use
     */
    public $environment = 'local';

    /**
     * @var bool
     * Verbose mode. More output for debugging.
     */
    public $verbose = false;

    /**
     * @var Environment
     */
    protected $envConfig;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return [
            'verbose',
            'environment',
        ];
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return [
            'v'   => 'verbose',
            'env' => 'environment',
        ];
    }

    public function vout(string $debugText)
    {
        if ($this->verbose) {
            $this->stdout('DEBUG ' . $debugText . PHP_EOL, Console::FG_PURPLE);
        }
    }

    /**
     * This will look by the passed env name whether it's the key/main name to the
     * environment or if it's the ebEnvironmentName
     *
     * @see EvoConfig::getEnvironment()
     * @see EvoConfig::getEnvironmentByEbName()
     *
     * @return Environment|int|null
     */
    protected function getEnvironment()
    {
        if (! $this->envConfig) {
            $this->vout('Determining environment.');
            $evoConfig = $this->getConfig();
            if (! $this->envConfig = $evoConfig->getEnvironment($this->environment)) {
                $this->envConfig = $evoConfig->getEnvironmentByEbName($this->environment);
            }

            if (! $this->envConfig) {
                $this->stderr('Environment not configured! ' . $this->environment . PHP_EOL, Console::FG_RED);
                return ExitCode::CONFIG;
            }

            /**
             * Set on the App
             */
            Evo::getInstance()->getConfig()->setEnvironment(
                $this->envConfig
            );

            $this->vout('Environment found. ' . $this->envConfig->name);
        }

        return $this->envConfig;
    }

    protected function getConfig()
    {
        return Evo::getInstance()->getConfig()->load();
    }
}