<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 11:25 PM
 */

namespace flipboxlabs\evo\services;


use yii\base\Component;

class Twig extends Component
{
    const CACHE_DIR = EVO_ROOT . '/cache';
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function init()
    {
        parent::init();

        if (! file_exists(static::CACHE_DIR)) {
            mkdir(static::CACHE_DIR);
        }
        $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => static::CACHE_DIR,
        ]);
    }

    /**
     * @param $template
     * @param $variables
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($template, $variables)
    {
        return $this->twig->render(
            $template,
            $variables
        );
    }
}