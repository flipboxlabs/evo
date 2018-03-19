<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/19/17
 * Time: 4:32 PM
 */

namespace flipboxlabs\evo;

use flipboxlabs\evo\modules\docker\Docker;
use flipboxlabs\evo\modules\webserver\WebServer;
use flipboxlabs\evo\services\ConfigService;
use yii\console\Application;

class Evo extends Application
{
    public $enableCoreCommands = false;

    /**
     * @return Docker
     */
    public function getDocker()
    {
        return $this->getModule('docker');
    }

    /**
     * @return WebServer
     */
    public function getWebServer()
    {
        return $this->getModule('webserver');
    }

    /**
     * @return ConfigService
     */
    public function getConfig()
    {
        return $this->get('config');
    }
}