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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Swop\ConfigStore\Exception\AlreadyExistingConfigKeyException;
use Swop\ConfigStore\Exception\UnknownConfigKeyException;

/**
 * Registered application, which can own several config items values.
 *
 * @package Swop\ConfigStore\Model
 *
 * @author  Sylvain Mauduit <swop@swop.io>
 */
class App
{
    /** @var int $id */
    private $id;
    /** @var string $name */
    private $name;
    /** @var string $description */
    private $description;
    /** @var AppGroup $group */
    private $group;
    /** @var string $accessKey */
    private $accessKey;
    /** @var ConfigItem[] $configItems */
    private $configItems;

    public function __construct($name, $accessKey)
    {
        $this->configItems = new ArrayCollection();

        $this->setName($name);
        $this->setAccessKey($accessKey);
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @return \Swop\ConfigStore\Model\ConfigItem[]
     */
    public function getConfigItems()
    {
        return $this->configItems;
    }

    public function setConfigItems($configItems)
    {
        if (is_array($configItems)) {
            $this->configItems = new ArrayCollection($configItems);
        } elseif ($configItems instanceof Collection) {
            $this->configItems = $configItems;
        }
    }

    /**
     * Adds a config item in the app config set
     *
     * @param ConfigItem $configItem
     *
     * @throws \Swop\ConfigStore\Exception\AlreadyExistingConfigKeyException
     */
    public function addConfigItem(ConfigItem $configItem)
    {
        if ($this->hasConfigItem($configItem)) {
            throw new AlreadyExistingConfigKeyException($configItem, $this);
        }

        $configItem->setApp($this);

        $this->configItems[] = $configItem;
    }

    /**
     * Checks if the current app already owns the given config item
     *
     * @param ConfigItem|string $configKey Config item or its key
     *
     * @return bool
     */
    public function hasConfigItem($configKey)
    {
        if ($configKey instanceof ConfigItem) {
            $configKey = $configKey->getKey();
        }

        return 0 < count($this->filterConfigItems($configKey));
    }

    public function getConfigItem($configKey)
    {
        $configItems = $this->filterConfigItems($configKey);

        if (0 === count($configItems)) {
            throw new UnknownConfigKeyException($configKey, $this);
        }

        return array_shift($configItems);
    }

    /**
     * Filter the config items by config key
     *
     * @param string $configKey
     *
     * @return array
     */
    protected function filterConfigItems($configKey)
    {
        return array_filter(
            $this->configItems->toArray(),
            function ($item) use ($configKey) {
                /** @var ConfigItem $item */
                if ($item->getKey() === $configKey) {
                    return true;
                }

                return false;
            }
        );
    }

    /**
     * Get all the config keys
     *
     * @return array
     */
    public function getConfigKeys()
    {
        return array_reduce(
            $this->configItems->toArray(),
            function ($keys, $item) {
                $keys[] = $item->getKey();

                return $keys;
            },
            []
        );
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \Swop\ConfigStore\Model\AppGroup|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the description attribute
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the group attribute
     *
     * @param \Swop\ConfigStore\Model\AppGroup $group
     *
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @param string $name
     *
     * @throws \LogicException
     *
     * @return $this
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \LogicException('The application name must be a non-empty string');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param string $accessKey
     *
     * @throws \LogicException
     *
     * @return $this
     */
    public function setAccessKey($accessKey)
    {
        if (!is_string($accessKey) || empty($accessKey)) {
            throw new \LogicException('The application access key must be a non-empty string');
        }

        $this->accessKey = $accessKey;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigArray()
    {
        return array_reduce(
            $this->getConfigItems()->toArray(),
            function ($configArray, $item) {
                $configArray[$item->getKey()] = $item->getValue();

                return $configArray;
            },
            []
        );
    }
}
