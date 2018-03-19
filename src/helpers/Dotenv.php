<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/22/17
 * Time: 10:29 AM
 */

namespace flipboxlabs\evo\helpers;

use Dotenv\Dotenv as BaseDotenv;

class Dotenv extends BaseDotenv
{

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @inheritdoc
     */
    protected function loadData($overload = false)
    {
        $this->loader = new Loader($this->filePath, ! $overload);

        return $this->loader->load();
    }

    /**
     * Return all variables in the .env file
     *
     * @return array
     */
    public function getEnvironmentVariables()
    {
        return $this->loader->getEnvironmentVariables();
    }
}