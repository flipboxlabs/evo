<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/20/18
 * Time: 9:07 PM
 */

namespace flipboxlabs\evo\modules\cloudformation;


use flipboxlabs\evo\modules\cloudformation\services\Build;
use yii\base\Module;

class Cloudformation extends Module
{

    public function init()
    {
        parent::init();
        $this->initComponents();
    }

    protected function initComponents()
    {
        $this->setComponents([
            'build' => Build::class,
        ]);
    }

    /**
     * @return Build
     */
    public function getBuild()
    {
        return $this->get('build');
    }

}