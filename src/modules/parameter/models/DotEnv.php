<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\parameter\models;


use flipboxlabs\evo\Evo;
use yii\base\Model;

class DotEnv extends Model
{
    public $name;
    public $value;

    /**
     * Use the service to format the row correctly
     * @return string
     */
    public function __toString()
    {
        return Evo::getInstance()->getParameter()->getParameter()->toDotEnv($this);
    }
}