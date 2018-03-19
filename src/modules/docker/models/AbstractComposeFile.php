<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 9:23 PM
 */

namespace flipboxlabs\evo\modules\docker\models;


use yii\base\Model;

abstract class AbstractComposeFile extends Model
{

    /**
     * @var string
     */
    public $phpImage;

    /**
     * @var string
     */
    public $webImage;

    /**
     * @var string
     */
    public $dbImage;

}