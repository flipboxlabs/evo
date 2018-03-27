<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/17/18
 * Time: 12:39 PM
 */

namespace flipboxlabs\evo\models;


use flipboxlabs\evo\Evo;
use Symfony\Component\Yaml\Yaml;
use yii\base\Model;

class EvoConfig extends Model
{
    /**
     * @var Environment[]
     */
    protected $environments = [];

    /**
     * @var string
     */
    public $projectName;

    /**
     * @var string
     */
    public $ebApplicationName;

    public function __construct(string $filePath = null, array $config = [])
    {

        $mergedConfig = [];

        if ($filePath) {
            $mergedConfig = array_merge(
                Evo::getInstance()->getConfig()->mergeConfig($filePath, $config),
                $config
            );
        }

        parent::__construct($mergedConfig);
    }

    public function attributes()
    {
        return array_merge(
            [
                'projectName',
                'ebApplicationName',
                'environments',
            ],
            parent::attributes()
        );
    }

    /**
     * @param array $environments
     */
    public function setEnvironments(array $environments)
    {
        $this->environments = [];
        foreach ($environments as $name => $value) {
            $this->environments[$name] = new Environment($name, $value);
        }
    }

    /**
     * @return Environment[]
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * @param $name
     * @return Environment|null
     */
    public function getEnvironment($name)
    {
        if (isset($this->environments[$name])) {
            return $this->environments[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @return Environment|null
     */
    public function getEnvironmentByEbName($name)
    {
        $returnEnvironment = null;
        foreach ($this->environments as $environment) {
            if ($environment->ebEnvironmentName === $name) {
                $returnEnvironment = $environment;
                break;
            }
        }

        return $returnEnvironment;
    }

    /**
     * @param Environment $environment
     * @return $this
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments[$environment->name] = $environment;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasEnvironment($name)
    {
        return isset($this->environments[$name]);
    }
}