<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\constants;


class Evo implements ConstantsInterface
{
    const EVO_CONFIG_DIR = APP_ROOT . '/.evo';
    const EVO_CONFIG_FILE = self::EVO_CONFIG_DIR . '/config';

    public static function all(): array
    {
        return [
            static::EVO_CONFIG_DIR,
            static::EVO_CONFIG_FILE,
        ];
    }

}