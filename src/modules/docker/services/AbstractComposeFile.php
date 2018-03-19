<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 9:23 PM
 */

namespace flipboxlabs\evo\modules\docker\services;


use Symfony\Component\Yaml\Yaml;
use yii\base\Component;
use flipboxlabs\evo\modules\docker\models\AbstractComposeFile as AbstractModel;
use yii\base\Exception;

abstract class AbstractComposeFile extends Component
{

    const DOCKER_TEMPLATES = EVO_ROOT . '/evo-templates/docker-compose/';
    const APACHE_TEMPLATE = self::DOCKER_TEMPLATES . 'apache.yml';
    const NGINX_TEMPLATE = self::DOCKER_TEMPLATES . 'nginx.yml';
    const WEB_SERVICE_NAME = 'web';
    const PHP_SERVICE_NAME = 'php';
    const DB_SERVICE_NAME = 'db';

    const DB_IMAGES = [
        'latest' => 'mysql:5.6',
        '5'      => 'mysql:5.6',
        '5.6'    => 'mysql:5.6',
    ];

    /**
     * @return array
     */
    abstract public function getWebOptions(): array;

    /**
     * @return array
     */
    abstract public function getPhpOptions(): array;

    /**
     * @return AbstractModel
     */
    abstract public function newModel(): AbstractModel;

    /**
     * @return array
     */
    public function getDbOptions(): array
    {
        return static::DB_IMAGES;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getTemplate(): string
    {
        $template = null;
        switch (true) {
            case $this instanceof ApacheComposeFile:
                $template = static::APACHE_TEMPLATE;
                break;
            case $this instanceof NginxComposeFile:
                $template = static::NGINX_TEMPLATE;
                break;
        }

        if (! $template) {
            throw new Exception('What happened here?!');
        }
        return file_get_contents($template);
    }

    /**
     * @param AbstractModel $fileModel
     * @param string $filePath
     * @return mixed
     * @throws Exception
     */
    public function prepare(AbstractModel $fileModel)
    {
        $template = Yaml::parse(
            $this->getTemplate()
        );

        if ($fileModel->webImage) {
            $template['services'][static::WEB_SERVICE_NAME]['image'] = $fileModel->webImage;
        }

        if ($fileModel->phpImage) {
            $template['services'][static::PHP_SERVICE_NAME]['image'] = $fileModel->phpImage;
        }


        if ($fileModel->dbImage) {
            $template['services'][static::DB_SERVICE_NAME]['image'] = $fileModel->dbImage;
        }

        return $template;
    }

    /**
     * @param array $template
     * @param string $filePath
     * @param int $inline
     */
    public function save(array $template, string $filePath, $inline = 5)
    {
        file_put_contents(
            $filePath,
            $this->dump($template, $inline)
        );
    }

    /**
     * @param $template
     * @param int $inline
     * @return string
     */
    public function dump($template, $inline = 5)
    {

        return Yaml::dump($template, $inline);
    }
}