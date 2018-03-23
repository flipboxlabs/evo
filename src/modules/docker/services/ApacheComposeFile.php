<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 9:23 PM
 */

namespace flipboxlabs\evo\modules\docker\services;

use flipboxlabs\evo\modules\docker\models\AbstractComposeFile as AbstractModel;
use flipboxlabs\evo\modules\docker\models\ApacheComposeFile as ApacheModel;

class ApacheComposeFile extends AbstractComposeFile
{
    const AMAZONLINIX_APACHE_71 = 'flipbox/php:71-amazonlinux-apache';
    const AMAZONLINIX_APACHE_70 = 'flipbox/php:70-amazonlinux-apache';

    const WEB_IMAGES = [
        'latest' => self::AMAZONLINIX_APACHE_71,
        'php-71' => self::AMAZONLINIX_APACHE_71,
        'php-70' => self::AMAZONLINIX_APACHE_70,
    ];

    const WEB_VOLUMES_DIR = EVO_ROOT . '/evo-templates/httpd.d/';
    const PHP_VOLUMES_DIR = EVO_ROOT . '/evo-templates/php.d/';

    /**
     * @inheritdoc
     */
    public function getWebOptions(): array
    {
        return static::WEB_IMAGES;
    }


    /**
     * @inheritdoc
     */
    public function getPhpOptions(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function newModel(): AbstractModel
    {
        return new ApacheModel;
    }
}