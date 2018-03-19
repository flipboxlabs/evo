<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 11:17 PM
 */

namespace flipboxlabs\evo\modules\webserver;


use flipboxlabs\evo\modules\webserver\services\VirtualHost;
use yii\base\Module;

class WebServer extends Module
{

    public function init()
    {
        parent::init();

        $this->initComponents();

    }

    public function initComponents()
    {
        $this->setComponents([
            'virtualhost' => VirtualHost::class,
        ]);
    }

    /**
     * @return VirtualHost
     */
    public function getVirtualHost()
    {
        return $this->get('virtualhost');
    }
}