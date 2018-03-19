<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/19/17
 * Time: 4:06 PM
 */

namespace flipboxlabs\evo\controllers;

use flipboxlabs\evo\helpers\Dotenv;
use yii\console\ExitCode;
use yii\helpers\Console;

class ParamController extends AbstractAwsController
{

    protected $environmentalVariables = [];

    public function actionPrintDotenv()
    {
        $this->getParameter();
        $contents = $this->arrayToEnvFile($this->environmentalVariables);
        $this->stdout($contents);
    }

    /**
     * @return int
     */
    public function actionSetAll()
    {
        $environmentVariables = [
            'DB_DRIVER'         => 'mysql',
            'DB_SERVER'         => 'localhost',
            'DB_USER'           => 'root',
            'DB_PASSWORD'       => 'password',
            'DB_DATABASE'       => 'craft',
            'CRAFT_ENVIRONMENT' => 'DEV',
        ];


        if ($this->exists()) {
            $environmentVariables = $this->loadFromSsmParams();
        } else {
            try {
                $environmentVariables = $this->loadFromLocalFile();
                $this->prependName($environmentVariables);
            } catch (\Dotenv\Exception\InvalidPathException $e) {
                $this->stdout(PHP_EOL . 'No .env found, moving on using the defaults.' . PHP_EOL . PHP_EOL, Console::FG_YELLOW);
            }

        }


        foreach ($environmentVariables as $name => $value) {
            $baseName = $this->ansiFormat($this->getBaseName($name), Console::FG_GREEN);
            $ansiName = $this->ansiFormat($name, Console::FG_GREY);
            $input = $this->prompt("Set {$baseName} ({$ansiName})", [
                'default' => $value,
            ]);
            if ($input != 'skip') {
                $this->environmentalVariables[$name] = $input;
            }
        }

        if (! $this->environmentalVariables) {
            $this->stdout('Nothing to save. Exiting.' . PHP_EOL, Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $this->stdout('Here is what the file will look like:' . PHP_EOL, Console::FG_YELLOW);
        $exampleFile = $this->arrayToEnvFile($this->environmentalVariables);
        $this->stdout(
            $exampleFile,
            Console::FG_GREEN
        );

        if ($this->environmentalVariables && 'yes' == $this->prompt('Would you like to save this?', [
                'default'  => 'yes',
                'required' => true,
            ])) {

        }

        $this->setParameters($this->environmentalVariables);
        return ExitCode::OK;
    }

    /**
     * Set a parameter in AWS' System Manager Parameter Store
     *
     * @param string $name
     * @param string $value
     * @return int
     */
    public function actionSet($name = null, $value = null)
    {

        if (! $name) {
            $name = $this->prompt('Which parameter would you like to update?');
        }

        if (! $value) {
            $value = $this->prompt(sprintf('What would you like to set %s to?', $name));
        }

        $this->stdout(
            sprintf('%s = %s', $name, $value) . PHP_EOL
        );

        return ExitCode::OK;
    }

    protected function loadFromLocalFile($path = APP_ROOT, $filename = '.env')
    {
        $dotEnv = new Dotenv($path, $filename);
        $dotEnv->load();
        $this->stdout("Loading .env file ... " . PHP_EOL);
        $this->stdout("Use \"skip\" to not include the item. " . PHP_EOL, Console::FG_YELLOW);
        return $dotEnv->getEnvironmentVariables();
    }

    protected function loadFromSsmParams()
    {
        return $this->getParameter();
    }

    /**
     * @return bool
     */
    protected function exists()
    {
        /**
         * this needs to be a soft comparison
         */
        return null != $this->getParameter();
    }

    protected function getParameter($env = null)
    {
        if (! $env) {
            $env = $this->environment;
        }

        try {
            $result = $this->getClient()->getParametersByPath([
                'Path'           => $this->makeName(),
                'WithDecryption' => true,
                'Recursive'      => true,
            ]);

            foreach ($result['Parameters'] as $parameter) {
                $this->environmentalVariables[$parameter['Name']] = $parameter['Value'];
            }

        } catch (\Aws\Ssm\Exception\SsmException $exception) {
            $result = null;
        }

        return $this->environmentalVariables;
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeName($name = null)
    {
        return '/dotenv/' . $this->environment . '/' . ($name ?: '');
    }

    /**
     * @param $environmentalVariables
     * @return mixed
     */
    protected function prependName($environmentalVariables)
    {
        foreach ($environmentalVariables as $key => $value) {
            $environmentalVariables[$this->makeName($key)] = $value;
            unset($environmentalVariables[$key]);
        }

        return $environmentalVariables;
    }

    protected function getBaseName($name)
    {
        $nameParts = explode('/', $name);
        return array_pop($nameParts);
    }

    protected function setParameters(array $params)
    {
        foreach ($params as $name => $value) {
            $result = $this->getClient()->putParameter([
                'Name'      => $this->makeName($name),
                'Type'      => 'SecureString',
                'Value'     => $value,
                'Overwrite' => true,
            ]);

        }
    }

    protected function arrayToEnvFile(array $params)
    {
        $contents = '#this file was automatically generated at ' . (new \DateTime())->format('Y-m-d m:i:s') . PHP_EOL;
        foreach ($params as $name => $value) {
            $contents .= sprintf('%s="%s"' . PHP_EOL, $this->getBaseName($name), $value);
        }
        return $contents;
    }


}