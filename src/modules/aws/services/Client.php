<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\aws\services;


use Aws\AwsClient;
use Aws\Sdk;
use flipboxlabs\evo\Evo;
use yii\base\Component;

class Client extends Component
{
    public $client;
    /**
     * @var string
     */
    public $region;
    /**
     * @var string
     */
    public $profile;

    protected function loadFromConfig($environmentName = null)
    {
        $config = Evo::getInstance()->getConfig()->load();
        $config->getEnvironment($environmentName);
    }


    public function getClient($config = [])
    {

        if (! $this->client) {
            $this->client = new Sdk(array_merge(
                [
                    'profile' => $this->profile,
                    'region'  => $this->region,
                    'version' => '2014-11-06',
                ],
                $config
            ));
        }

        return $this->client;
    }
}