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
}