<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/20/18
 * Time: 4:03 PM
 */

namespace flipboxlabs\evo\constants;


class TwigTemplates implements ConstantsInterface
{

    const CONFIG_HEADER = 'evo/config.twig';

    public static function all(): array
    {
        return [
            self::CONFIG_HEADER,
        ];
    }
}