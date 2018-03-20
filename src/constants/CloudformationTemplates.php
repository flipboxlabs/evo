<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/20/18
 * Time: 4:35 PM
 */

namespace flipboxlabs\evo\constants;


class CloudformationTemplates
{
    const EVO_CF_ROOT = EVO_ROOT . '/evo-templates/cloudformation';
    const CODECOMMIT = self::EVO_CF_ROOT . '/codecommit.yaml';
    const EB = self::EVO_CF_ROOT . '/eb.yaml';
}