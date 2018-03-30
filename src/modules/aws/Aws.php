<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\aws;

use flipboxlabs\evo\modules\aws\services\Client;
use flipboxlabs\evo\modules\aws\services\Config;
use yii\base\Module;

class Aws extends Module
{

    public function init()
    {
        parent::init();

    }

    public function initComponents()
    {
        $this->setComponents([
            'client' => Client::class,
        ]);
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->get('client');
    }
}