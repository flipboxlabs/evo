<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\aws\services;


use Aws\Credentials\CredentialProvider;
use Aws\Result;
use Aws\Sdk;
use Aws\Sts\StsClient;
use flipboxlabs\evo\modules\aws\credentials\SourceProfileProvider;
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
    public $version;

    public $tokenCode;
    public $roleArn;
    public $serialNumber;
    public $sourceProfile;
    public $credentialResult;

    public function getClientConfig($config = [])
    {
        //support mfa
        \Yii::configure($this, $config);

        $baseConfig = [
            'region'  => $this->region,
            'version' => 'latest',
        ];

        /**
         * This comes from the cached credentials
         */
        if ($this->credentialResult && $this->credentialResult instanceof Result) {
            $baseConfig['credentials'] = (new StsClient($baseConfig))->createCredentials($this->credentialResult);
        }

        /**
         * AssumeRole support
         */
        if ($this->roleArn) {
            $roleParams = [];
            $roleParams['RoleSessionName'] = 'evo';
            if ($this->tokenCode) {
                $roleParams['TokenCode'] = $this->tokenCode;
            }
            if ($this->roleArn) {
                $roleParams['RoleArn'] = $this->roleArn;
            }

            if ($this->serialNumber) {
                $roleParams['SerialNumber'] = $this->serialNumber;
                $roleParams['RoleSessionName'] .= '@' . preg_replace('#.*/([A-Za-z0-9_=,.@-]+)$#', '\\1', $this->serialNumber);
            }

            return array_merge(
                $baseConfig,
                [
                    'credentials' => CredentialProvider::memoize(
                        new SourceProfileProvider(
                            $this->profile,
                            [
                                'client'             => new StsClient([
                                    'profile' => $this->sourceProfile,
                                    'region'  => $this->region,
                                    'version' => 'latest',
                                ]),
                                'assume_role_params' => $roleParams,
                            ])
                    )
                ]);

        }

        /**
         * Just profile
         */
        if ($this->profile && ! $this->credentialResult) {
            $baseConfig['profile'] = $this->profile;
        }

        return $baseConfig;

    }

    /**
     * @param array $config
     * @return Sdk
     */
    public function getClient($config = [])
    {

        if (! $this->client) {
            $config = $this->getClientConfig($config);
            $this->client = new Sdk($config);
        }

        return $this->client;
    }


}