<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Model;

/**
 * Configuration key/value item
 *
 * @package Swop\ConfigStore\Model
 *
 * @author  Sylvain Mauduit <swop@swop.io>
 */
class ConfigItem
{
    /** @var App $app */
    private $app;
    /** @var string $key */
    private $key;
    /** @var string $value */
    private $value;

    /**
     * @param App    $app
     * @param string $key
     * @param string $value
     *
     * @throws \LogicException
     */
    public function __construct(App $app, $key, $value)
    {
        $this->app   = $app;

        if (!is_string($key)) {
            throw new \LogicException('The config value must be a string');
        }

        $this->value = $value;

        if (!is_string($key) || empty($key)) {
            throw new \LogicException('The config key must be a non-empty string');
        }

        $this->key = $key;
    }

    /**
     * @return \Swop\ConfigStore\Model\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param \Swop\ConfigStore\Model\App $app
     *
     * @return $this
     */
    public function setApp(App $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
