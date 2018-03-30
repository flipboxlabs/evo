<?php

namespace flipboxlabs\evo\modules\aws\controllers;

use Aws\Api\DateTimeResult;
use Aws\Credentials\CredentialProvider;
use Aws\Result;
use Aws\Sdk;
use Aws\Ssm\SsmClient;
use flipboxlabs\evo\controllers\AbstractController;
use flipboxlabs\evo\Evo;
use flipboxlabs\evo\models\Environment;
use flipboxlabs\evo\modules\aws\credentials\AssumeRoleCredentialProviderWithMfa;
use flipboxlabs\evo\modules\aws\credentials\CredentialCache;
use flipboxlabs\evo\modules\aws\credentials\SourceProfileProvider;
use flipboxlabs\evo\modules\aws\services\Client;
use yii\helpers\Console;

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
     * @var string|null
     */
    public $mfaToken;

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
                'mfaToken',
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

        if ($this->mfaToken) {
            $config['tokenCode'] = $this->mfaToken;
        }

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

            if (isset($config['profile'])) {
                $cache = new CredentialCache();

                /** @var Result $creds */
                $creds = $cache->get($config['profile']);

                /** @var DateTimeResult $expires */
                $expires = $creds['Credentials']['Expiration'];
                if ($expires <= ($now = new \DateTime('+20 seconds'))) {
                    $this->vout('Found cached creds but they are expired, removing them now.');
                    $cache->remove($config['profile']);
                } else {
                    $config['credentialResult'] = $creds;
                    $this->vout('Found cached creds and the are still good. Using them.');
                }
            }

            if (
                /**
                 * Just move on if we already have creds
                 */
                ! isset($config['credentialResult']) &&
                /**
                 * is there a profile?
                 */
                isset($config['profile']) &&
                /**
                 * Parse Source Profile
                 */
                $assumeRoleParameters = SourceProfileProvider::getSourceProfile($config['profile'])
            ) {
                if (isset($assumeRoleParameters['source_profile'])) {

                    $this->vout('Found source profile. ' . $assumeRoleParameters['source_profile']);
                    $config['sourceProfile'] = $assumeRoleParameters['source_profile'];
                }
                if (isset($assumeRoleParameters['role_arn'])) {
                    $this->vout('Found role_arn, will assume role. ' . $assumeRoleParameters['role_arn']);
                    $config['roleArn'] = $assumeRoleParameters['role_arn'];
                }
                if (isset($assumeRoleParameters['mfa_serial'])) {
                    $this->vout('Found mfa_serial. ' . $assumeRoleParameters['mfa_serial']);
                    $config['serialNumber'] = $assumeRoleParameters['mfa_serial'];
                    if (! $this->mfaToken) {
                        $this->mfaToken = $this->prompt(
                            $this->ansiFormat('MFA Token:', Console::FG_YELLOW)
                        );
                        $config['tokenCode'] = $this->mfaToken;
                    }
                }
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