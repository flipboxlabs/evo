<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/22/17
 * Time: 10:43 AM
 */

namespace flipboxlabs\evo\helpers;


class Loader extends \Dotenv\Loader
{

    protected $envVars = [];


    /**
     * @return array
     */
    public function getEnvironmentVariables()
    {
        return $this->envVars;

    }

    public function setEnvironmentVariable($name, $value = null)
    {
        list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);

        // Don't overwrite existing environment variables if we're immutable
        // Ruby's dotenv does this with `ENV[key] ||= value`.
        if ($this->immutable && $this->getEnvironmentVariable($name) !== null) {
            return;
        }

        $this->envVars[$name] = $value;
    }
}