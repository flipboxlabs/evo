<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\parameter;

class Parameter extends \yii\base\Module
{

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->initComponents();
    }

    /**
     * Init Components
     */
    protected function initComponents()
    {
        $this->setComponents([
            'parameter' => services\Parameter::class,
        ]);
    }

    /**
     * @return services\Parameter
     */
    public function getParameter()
    {
        return $this->get('parameter');
    }
}