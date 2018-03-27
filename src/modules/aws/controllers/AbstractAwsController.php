<?php

namespace flipboxlabs\evo\modules\aws\controllers;

use Aws\Sdk;
use Aws\Ssm\SsmClient;
use flipboxlabs\evo\controllers\AbstractController;
use flipboxlabs\evo\Evo;
use flipboxlabs\evo\models\Environment;

abstract class AbstractAwsController extends AbstractController
{


    /**
     * @var string
     * The AWS Profile used for authentication
     */
    public $profile;

    /**
     * @var string
     * The AWS region
     */
    public $region;

    /**
     * @var bool
     * Explicitly tell evo to not read in the profile
     */
    public $noProfile = false;


    /**
     * @var Sdk
     */
    protected $client;

    abstract protected function initClient();

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            [
                'profile',
                'region',
                'noProfile',
            ],
            parent::options($actionID)
        );
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(
            [
                'r'  => 'region',
                'np' => 'noProfile',
            ],
            parent::optionAliases()
        );
    }


    /**
     * @return array
     */
    protected function loadConfig()
    {
        $config = [];

        /** @var Environment $environment */
        $environment = $this->getEnvironment();

        //profile
        if (! $this->noProfile) {
            $this->vout('Setting aws profile.');
            if ($this->profile) {
                $this->vout('Using profile explicitly set via option to: ' . $this->profile);
                $config['profile'] = $this->profile;
            } elseif ($environment && $environment->profile) {
                $this->vout('Using profile environment configuration: ' . $environment->profile);
                $config['profile'] = $environment->profile;
            } else {
                $this->vout('No profile found.');
            }

        }
        //region
        if ($this->region) {
            $this->vout('Using region explicitly set via option to: ' . $this->region);

            $config['region'] = $this->region;
        } elseif ($environment && $environment->region) {

            $this->vout('Using region environment configuration: ' . $environment->region);
            $config['region'] = $environment->region;
        } else {

            $this->vout('No region found.');
        }


        return $config;
    }
}