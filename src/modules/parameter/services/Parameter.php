<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\parameter\services;


use Aws\Result;
use flipboxlabs\evo\Evo;
use flipboxlabs\evo\modules\aws\services\Client;
use flipboxlabs\evo\modules\parameter\models\DotEnv;
use flipboxlabs\evo\constants\Evo as EvoConstants;
use flipboxlabs\evo\helpers\Dotenv as DotenvHelper;

class Parameter extends Client
{
    const PARAMETER_NAMESPACE = '/evo';

    /**
     * @param array $config
     * @return \Aws\Sdk|\Aws\Ssm\SsmClient
     */
    public function getClient($config = [])
    {
        if (! $this->client) {
            $config = $this->getClientConfig($config);
            $this->client = parent::getClient()->createSsm($config);
        }
        return $this->client;
    }

    /**
     * Get all of the local env properties from the evo config
     * @return DotEnv[]
     */
    public function getLocal($excludes = ['profile'])
    {
        $env = Evo::getInstance()->getConfig()->getEnvironment();
        $dotEnvs = [];

        foreach ($env->getAttributes() as $name => $value) {

            if (in_array($name, $excludes))
                continue;

            $dotEnvs[] = new DotEnv([
                'name'  => $env->getDotEnvName($name),
                'value' => $value,
            ]);
        }

        return $dotEnvs;
    }

    /**
     * Get all of the local envs from the .env file if it exists
     * @return array
     */
    public function getLocalDotEnvs()
    {
        if (! file_exists(EvoConstants::DOT_ENV_LOCATION)) {
            return [];
        }

        $dotEnv = new DotenvHelper(EvoConstants::DOT_ENV_LOCATION);
        $dotEnv->load();

        $dotEnvs = [];
        foreach ($dotEnv->getEnvironmentVariables() as $name => $value) {

            $dotEnvs[$this->makeDotEnvName($name)] = new DotEnv([
                'name'  => $name,
                'value' => $value,
            ]);
        }

        return $dotEnvs;
    }

    /**
     * @param $name
     * @param $value
     * @return Result
     */
    public function set($name, $value)
    {
        $path = $this->makeName($name);

        return $this->getClient()->putParameter([
            'Name'      => $path,
            'Type'      => 'SecureString',
            'Value'     => $value,
            'Overwrite' => true,
        ]);
    }

    /**
     * @param $name
     * @param bool $withDecryption
     * @return Result
     */
    public function get($name, $withDecryption = false)
    {
        return $this->getClient()->getParameter([
            'Name'           => $this->makeName($name),
            'WithDecryption' => $withDecryption,
        ]);
    }

    /**
     * @param $name
     * @return Result
     */
    public function delete($name)
    {
        return $this->getClient()->deleteParameter([
            'Name' => $this->makeName($name),
        ]);
    }

    /**
     * @param bool $withDecryption
     * @return DotEnv[]
     */
    public function getAllDotEnvs($withDecryption = false)
    {
        /** @var Result $results */
        $results = $this->getAllFromEnv($withDecryption);
        $dotEnvs = [];
        foreach ($results->search('Parameters') as $result) {
            $dotEnvs[$this->makeDotEnvName($result['Name'])] = new DotEnv([
                'name'  => $result['Name'],
                'value' => $result['Value'],
            ]);
        }

        return $dotEnvs;

    }

    /**
     * @param bool $withDecryption
     * @return Result
     */
    public function getAllFromEnv($withDecryption = false)
    {

        return $this->getClient()->getParametersByPath([
            'Path'           => $this->makeName(),
            'Recursive'      => true,
            'WithDecryption' => $withDecryption,
        ]);
    }

    /**
     * @param $name
     * @return string
     */
    public function makeName($name = null)
    {
        $config = Evo::getInstance()->getConfig()->load();
        $env = Evo::getInstance()->getConfig()->getEnvironment();
        return implode('/', [
            static::PARAMETER_NAMESPACE,
            $config->ebApplicationName,
            $env->name,
            ($name ?: '')
        ]);
    }

    /**
     *
     *
     * @param $path
     * @return null|string|string[]
     */
    public function makeDotEnvName($path)
    {
        return preg_replace('#^.*\/(\w+)$#', '\1', $path);
    }

    /**
     * @param $result
     * @return string
     */
    public function toDotEnv(DotEnv $item)
    {
        $dotEnv = implode('=', [
            $this->makeDotEnvName($item->name),
            /**
             * Wrap everything with double-quotes
             */
            '"' . $item->value . '"'
        ]);

        return $dotEnv;
    }
}