<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/21/17
 * Time: 4:02 PM
 */

namespace flipboxlabs\evo\services;


use flipboxlabs\evo\models\EvoConfig;
use Symfony\Component\Yaml\Yaml;
use yii\base\Component;

/**
 * What to save in the config
 * - Application Name
 * - Location of the .env
 *
 * Class ConfigService
 * @package flipboxlabs\evo\services
 */
class ConfigService extends Component
{
    const EVO_CONFIG_DIR = APP_ROOT . '/.evo';
    const EVO_CONFIG_FILE = self::EVO_CONFIG_DIR . '/config';

    public $file = self::EVO_CONFIG_FILE;
    /**
     * @var EvoConfig
     */
    public $config = [];


    public function mergeConfig(string $filePath, array $config = [])
    {
        $this->file = $filePath;

        $fromFile = [];
        if (file_exists($filePath)) {
            $fromFile = Yaml::parse(
                file_get_contents($filePath)
            );
        }

        return array_merge(
            $fromFile,
            $config
        );
    }

    /**
     * @param EvoConfig $config
     * @param bool $validate
     * @return bool
     */
    public function save(EvoConfig $config, $validate = true)
    {
        $result = false;

        if (! file_exists(static::EVO_CONFIG_DIR)) {
            mkdir(static::EVO_CONFIG_DIR);
        }

        if ($config->validate()) {
            $result = false !== file_put_contents($this->file, Yaml::dump($config->toArray(), 4));
        }

        return $result;
    }
}