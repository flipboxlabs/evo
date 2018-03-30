<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 */

namespace flipboxlabs\evo\modules\aws\credentials;


use Aws\CacheInterface;
use flipboxlabs\evo\Evo;

class CredentialCache implements CacheInterface
{

    const CACHE_DIR_NAME = 'cache';

    const KEY_PREFIX = 'aws';

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        if (! file_exists($this->getFilePath($key))) {
            return null;
        }

        $contents = file_get_contents($this->getFilePath($key));
        $result = unserialize($contents);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->createDirectory();

        $filepath = $this->getFilePath($key);

        $contents = serialize($value);

        file_put_contents($filepath, $contents);

    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        if (file_exists($this->getFilePath($key)))
            unlink($this->getFilePath($key));
    }

    /**
     * create all directory recursively
     */
    protected function createDirectory()
    {
        if (! file_exists($this->getDirectoryPath())) {
            mkdir($this->getDirectoryPath(), 0711, true);
        }
    }

    /**
     * @return string
     */
    protected function getDirectoryPath()
    {
        return Evo::getInstance()->getConfig()->getEvoHomeDirectory() . '/' . static::CACHE_DIR_NAME;
    }

    /**
     * @return bool
     */
    protected function hasDirectory()
    {
        return file_exists($this->getDirectoryPath());
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFilename(string $key)
    {
        return static::KEY_PREFIX . '_' . $key;
    }

    /**
     * @return string
     */
    protected function getFilePath($key)
    {
        $filename = $this->getFilename($key);

        return $this->getDirectoryPath() . '/' . $filename;
    }

    /**
     * @return bool
     */
    protected function hasFile(string $key)
    {
        $filename = $this->getFilename($key);

        return file_exists($this->getFilePath($filename));
    }

}