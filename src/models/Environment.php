<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/17/18
 * Time: 12:40 PM
 */

namespace flipboxlabs\evo\models;


use yii\base\Model;

class Environment extends Model
{
    public $name;
    public $siteUrl;
    public $webRoot;
    public $profile;
    public $region;
    public $ebEnvironmentName;

    const DOTENV_NAME_ENVIRONMENT = 'ENVIRONMENT';
    const DOTENV_NAME_SITE_URL = 'SITE_URL';
    const DOTENV_NAME_WEB_ROOT = 'WEB_ROOT';
    const DOTENV_NAME_PROFILE = 'PROFILE';
    const DOTENV_NAME_REGION = 'REGION';
    const DOTENV_NAME_EB_ENVIRONMENT_NAME = 'EB_ENVIRONMENT_NAME';


    public function __construct($name, array $config = [])
    {
        $this->name = $name;
        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return array_merge(
            [
                'profile' => 'AWS Profile',
                'region'  => 'AWS Region',
            ],
            parent::attributeLabels()
        );
    }

    /**
     * @param $name
     * @return string
     */
    public function getDotEnvName($name)
    {

        $nameMap = [
            'name'              => static::DOTENV_NAME_ENVIRONMENT,
            'siteUrl'           => static::DOTENV_NAME_SITE_URL,
            'webRoot'           => static::DOTENV_NAME_WEB_ROOT,
            'profile'           => static::DOTENV_NAME_PROFILE,
            'region'            => static::DOTENV_NAME_REGION,
            'ebEnvironmentName' => static::DOTENV_NAME_EB_ENVIRONMENT_NAME,
        ];

        return isset($nameMap[$name]) ? $nameMap[$name] : $name;
    }
}