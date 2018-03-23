<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 9:23 PM
 */

namespace flipboxlabs\evo\modules\docker\services;


use flipboxlabs\evo\modules\docker\models\AbstractComposeFile as AbstractModel;
use flipboxlabs\evo\modules\docker\models\NginxComposeFile as NginxModel;

class NginxComposeFile extends AbstractComposeFile
{
    const PHP_72 = 'flipbox/php:72-fpm-alpine';
    const NGINX_1_13 = 'nginx:1.13';

    const PHP_IMAGES = [
        'latest' => self::PHP_72,
        'php-72' => self::PHP_72,
    ];

    const WEB_IMAGES = [
        'latest' => self::NGINX_1_13,
        '1.13'   => self::NGINX_1_13,
    ];

    const WEB_VOLUMES_DIR = EVO_ROOT . '/evo-templates/nginx.d/';
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
        return static::PHP_IMAGES;
    }

    /**
     * @inheritdoc
     */
    public function newModel(): AbstractModel
    {
        return new NginxModel;
    }
}