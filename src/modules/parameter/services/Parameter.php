<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\parameter\services;


use Aws\Result;
use flipboxlabs\evo\Evo;
use flipboxlabs\evo\modules\aws\services\Client;
use flipboxlabs\evo\modules\parameter\models\DotEnv;

class Parameter extends Client
{
    const PARAMETER_NAMESPACE = '/evo/';

    public function getClient($config = [])
    {
        return parent::getClient($config)->createSsm();
    }

    /**
     * Get all of the local env properties
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

    public function get($name, $withDecryption = false)
    {
        return $this->getClient()->getParameter([
            'Name'           => $this->makeName($name),
            'WithDecryption' => $withDecryption,
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
            $dotEnvs[] = new DotEnv([
                'name'  => $result['Name'],
                'value' => $result['Value'],
            ]);
        }

        return $dotEnvs;

    }

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
        $env = Evo::getInstance()->getConfig()->getEnvironment();
        return static::PARAMETER_NAMESPACE . $env->name . '/' . ($name ?: '');
    }

    /**
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
            $item->value
        ]);

        return $dotEnv;
    }
}