<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\aws\credentials;

use Aws\Exception\CredentialsException;
use Aws\Result;
use Aws\Sts\StsClient;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Credential provider that provides credentials via assuming a role
 * More Information, see: http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sts-2011-06-15.html#assumerole
 */
class SourceProfileProvider
{
    const ERROR_MSG = "Missing required 'AssumeRoleCredentialProvider' configuration option: ";
    const LOCAL_CREDENTIALS_FILE = '/.aws/credentials';
    const LOCAL_CONFIG_FILE = '/.aws/config';

    const CACHE_KEY = 'credentials';

    /** @var string */
    private $profile;

    /** @var StsClient */
    private $client;

    /** @var array */
    private $assumeRoleParams;

    /**
     * The constructor requires following configure parameters:
     *  - client: a StsClient
     *  - assume_role_params: Parameters used to make assumeRole call
     *
     * @param string $profile The profile name that has a source profile, not the source.
     * @param array $config Configuration options
     * @throws \InvalidArgumentException
     */
    public function __construct($profile, array $config = [])
    {

        if (! isset($config['assume_role_params'])) {
            throw new \InvalidArgumentException(self::ERROR_MSG . "'assume_role_params'.");
        }

        if (! isset($config['client'])) {
            throw new \InvalidArgumentException(self::ERROR_MSG . "'client'.");
        }

        $this->profile = $profile;
        $this->client = $config['client'];
        $this->assumeRoleParams = $config['assume_role_params'];
    }

    /**
     * Loads assume role credentials.
     *
     * @return PromiseInterface
     */
    public function __invoke()
    {
        $client = $this->client;
        $profile = $this->profile;
        return $client->assumeRoleAsync($this->assumeRoleParams)
            ->then(function (Result $result) use ($profile) {

                /**
                 * Set to cache
                 */
                $cache = new CredentialCache();
                $cache->set($profile, $result);

                /**
                 * return creds
                 */
                return $this->client->createCredentials($result);
            })->otherwise(function (\Exception $exception) {
                throw new CredentialsException(
                    "Error in retrieving assume role credentials.",
                    0,
                    $exception
                );
            });
    }

    /**
     * Gets the environment's HOME directory if available.
     *
     * Notice: This is pulled from the CredentialProvider class from the
     * aws php sdk.
     *
     * @return null|string
     */
    private static function getHomeDir()
    {
        // On Linux/Unix-like systems, use the HOME environment variable
        if ($homeDir = getenv('HOME')) {
            return $homeDir;
        }

        // Get the HOMEDRIVE and HOMEPATH values for Windows hosts
        $homeDrive = getenv('HOMEDRIVE');
        $homePath = getenv('HOMEPATH');

        return ($homeDrive && $homePath) ? $homeDrive . $homePath : null;
    }

    /**
     * Making this publicly available so that we can see if it's
     * a source profile or not.
     *
     * @param $profile
     * @return array|bool
     */
    public static function getSourceProfile($profile)
    {
        $filename = self::getHomeDir() . static::LOCAL_CONFIG_FILE;
        if (! is_readable($filename)) {
            return false;
        }
        $data = parse_ini_file($filename, true);
        if ($data === false) {
            return false;
        }

        foreach ($data as $profileName => $properties) {
            $returnIni = [];
            if (isset($properties['source_profile'])) {
                $returnIni['source_profile'] = $properties['source_profile'];
            }
            if (isset($properties['role_arn'])) {
                $returnIni['role_arn'] = $properties['role_arn'];
            }
            if (isset($properties['mfa_serial'])) {
                $returnIni['mfa_serial'] = $properties['mfa_serial'];
            }
            if ($profileName === $profile || $profileName === 'profile ' . $profile) {
                return $returnIni;
            }

        }

        return false;
    }

}
