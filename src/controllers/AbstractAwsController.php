<?php

namespace flipboxlabs\evo\controllers;

use Aws\Ssm\SsmClient;
use yii\console\Controller;

abstract class AbstractAwsController extends Controller
{
    /**
     * @var string
     * Environment identifier you'd like to use
     */
    public $environment = 'dev';

    /**
     * @var string
     * The AWS Profile used for authentication
     */
    public $profile;

    /**
     * @var string
     * The AWS region
     */
    public $region = 'us-east-1';

    private $client;

    public function options($actionID)
    {
        return [
            'environment',
            'profile',
            'region',
        ];
    }

    public function optionAliases()
    {
        return ['env' => 'environment'];
    }

    /**
     * @return SsmClient
     */
    protected function getClient()
    {

        if (! $this->client) {
            $this->client = new SsmClient([
                'profile' => $this->profile,
                'region'  => $this->region,
                'version' => '2014-11-06',
            ]);

        }

        return $this->client;
    }
}