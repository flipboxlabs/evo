<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\constants;


class Evo implements ConstantsInterface
{
    /**
     * Location of the evo directory
     */
    const EVO_CONFIG_DIR = APP_ROOT . '/.evo';

    /**
     * Location of the config file
     */
    const EVO_CONFIG_FILE = self::EVO_CONFIG_DIR . '/config';

    /**
     * Where the dot env is supposed to be
     */
    const DOT_ENV_LOCATION = APP_ROOT;

    /**
     * Just return all constants
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            static::EVO_CONFIG_DIR,
            static::EVO_CONFIG_FILE,
            static::DOT_ENV_LOCATION,
        ];
    }

}