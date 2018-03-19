<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 8:44 PM
 */

namespace flipboxlabs\evo\modules\docker\actions;


use flipboxlabs\evo\Evo;
use flipboxlabs\evo\modules\docker\services\AbstractComposeFile;

class Apache extends AbstractAction
{
    /**
     * @inheritdoc
     */
    protected function getService(): AbstractComposeFile
    {
        return Evo::getInstance()->getDocker()->getApache();
    }
}