<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 8:38 PM
 */

namespace flipboxlabs\evo\modules\docker;


use flipboxlabs\evo\modules\docker\services\ApacheComposeFile;
use flipboxlabs\evo\modules\docker\services\NginxComposeFile;
use yii\base\Module;

class Docker extends Module
{

    public function init()
    {
        parent::init();

        $this->initComponents();
    }

    protected function initComponents()
    {
        $this->setComponents([
            'apache' => ApacheComposeFile::class,
            'nginx'  => NginxComposeFile::class,
        ]);
    }

    /**
     * @return ApacheComposeFile
     */
    public function getApache()
    {
        return $this->get('apache');
    }

    /**
     * @return NginxComposeFile
     */
    public function getNginx()
    {
        return $this->get('nginx');
    }
}