<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 12/19/17
 * Time: 4:06 PM
 */

namespace flipboxlabs\evo\modules\parameter\controllers;

use flipboxlabs\evo\Evo;
use yii\console\ExitCode;
use yii\helpers\Console;
use flipboxlabs\evo\modules\aws\controllers\AbstractAwsController;

class ParamController extends AbstractAwsController
{

    protected $environmentalVariables = [];

    protected function initClient()
    {
        $config = $this->loadConfig();
        $this->client = Evo::getInstance()->getParameter()->getParameter()->getClient($config);
    }

    public function actionPrintDotenv()
    {
        $this->initClient();

        /**
         * Prepend the local environment values from the EVO Config
         */

        $dotEnvs = array_merge(
            $this->getService()->getLocal(),
            $this->getService()->getAllDotEnvs(true)
        );

        foreach ($dotEnvs as $dotEnv) {
            $this->stdout(
                $this->getService()->toDotEnv($dotEnv) . PHP_EOL,
                Console::FG_CYAN
            );
        }

        return ExitCode::OK;
    }

    /**
     * Get a parameter in AWS' System Manager Parameter Store
     *
     * @param string $name
     * @return int
     */
    public function actionGet($name = null)
    {
        $this->initClient();

        $result = $this->getService()->get($name, true);
        if ($param = $result->search('Parameter')) {

            $this->vout(
                'Parameter Found!'
            );

            $dotEnv = new \flipboxlabs\evo\modules\parameter\models\DotEnv([
                'name'  => $param['Name'],
                'value' => $param['Value'],
            ]);

            $this->stdout(
                $this->getService()->toDotEnv($dotEnv)
                , Console::FG_CYAN
            );
        }
        return ExitCode::OK;

    }


    /**
     * Set a parameter in AWS' System Manager Parameter Store
     *
     * @param string $name
     * @param string $value
     * @return int
     */
    public function actionSet($name = null, $value = null)
    {

        $this->initClient();


        if (! $name) {
            $name = $this->prompt($this->ansiFormat('Parameter Name: ', Console::FG_CYAN), [

            ]);
        }

        if (! $value) {
            $value = $this->prompt(sprintf($this->ansiFormat('Parameter Value for %s: ', Console::FG_CYAN), $this->ansiFormat($name, Console::FG_YELLOW)));
        }

        $this->stdout(
            sprintf('Settings %s = %s', $this->ansiFormat($name, Console::FG_YELLOW), $this->ansiFormat('***', Console::FG_YELLOW) . PHP_EOL)
        );

        Evo::getInstance()->getParameter()->getParameter()->set($name, $value);

        return ExitCode::OK;
    }


    /**
     * UTILs
     */

    /**
     * @return \flipboxlabs\evo\modules\parameter\services\Parameter
     */
    protected function getService()
    {
        return Evo::getInstance()->getParameter()->getParameter();
    }

}