<?php
/**
 * Created by PhpStorm.
 * User: dsmrt
 * Date: 3/16/18
 * Time: 8:56 PM
 */

namespace flipboxlabs\evo\modules\docker\actions;


use flipboxlabs\evo\modules\docker\services\AbstractComposeFile;
use flipboxlabs\evo\modules\docker\models\AbstractComposeFile as ComposeFileModel;
use yii\base\Action;
use yii\console\ExitCode;
use yii\helpers\Console;

abstract class AbstractAction extends Action
{
    /**
     * @return AbstractComposeFile
     */
    abstract protected function getService(): AbstractComposeFile;

    /**
     * @return int
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $fileModel = $this->getService()->newModel();

        /**
         * Configure Services
         */
        $this->promptWebService($fileModel);
        $this->promptPhpService($fileModel);
        $this->promptDbService($fileModel);

        $this->promptVolumes($fileModel);

        /**
         * Configure docker-compose.yml file location
         */
        $fileLocation = $this->promptFileLocation();

        /**
         * Get template
         */
        $template = $this->getService()->prepare($fileModel);

        /**
         * Preview file
         */
        $this->controller->stdout('Preview file: ' . PHP_EOL, Console::FG_BLUE);
        $this->controller->stdout($this->getService()->dump($template) . PHP_EOL, Console::FG_GREEN);

        /**
         * Confirm save
         */
        if (! $this->controller->confirm('Look good?', true)) {
            $this->controller->stdout('Ok, not saving the compose file. Exiting.' . PHP_EOL, Console::FG_YELLOW);
            return ExitCode::OK;
        }

        /**
         * Save file
         */
        $this->getService()->save($template, $fileLocation);
        $this->controller->stdout('File saved!' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * @return string
     */
    protected function promptFileLocation()
    {
        $fileLocation = $this->controller->prompt('Where should the file be saved?', [
            'default' => APP_ROOT . '/docker-compose.yml',
        ]);

        if (file_exists($fileLocation)) {
            if (! $this->controller->confirm(
                'File exists. Do you want to overwrite this file?', true
            )) {
                return $this->promptFileLocation();
            } else {
                $this->controller->stdout(
                    'Overwriting: ' . $fileLocation . PHP_EOL,
                    Console::FG_YELLOW
                );
            }
        }
        return $fileLocation;
    }

    /**
     * @param ComposeFileModel $fileModel
     */
    protected function promptDbService(ComposeFileModel $fileModel)
    {
        if (! $options = $this->getService()->getDbOptions()) {
            return;
        }

        $optionKey = $this->controller->select(
            'Choose an image for the db service.',
            $options
        );

        $this->controller->stdout($options[$optionKey] . PHP_EOL, Console::FG_BLUE);
        $fileModel->dbImage = $options[$optionKey];
    }

    /**
     * @param ComposeFileModel $fileModel
     */
    protected function promptPhpService(ComposeFileModel $fileModel)
    {
        if (! $options = $this->getService()->getPhpOptions()) {
            return;
        }

        $optionKey = $this->controller->select(
            'Choose an image for the php service.',
            $options
        );

        $this->controller->stdout($options[$optionKey] . PHP_EOL, Console::FG_BLUE);
        $fileModel->phpImage = $options[$optionKey];
    }

    /**
     * @param ComposeFileModel $fileModel
     */
    protected function promptWebService(ComposeFileModel $fileModel)
    {
        if (! $options = $this->getService()->getWebOptions()) {
            return;
        }

        $optionKey = $this->controller->select(
            'Choose an image for the web service.',
            $options
        );

        $this->controller->stdout($options[$optionKey] . PHP_EOL, Console::FG_BLUE);
        $fileModel->webImage = $options[$optionKey];
    }

    protected function promptVolumes(ComposeFileModel $fileModel)
    {

    }
}