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

class AppGroup
{
    /** @var int $id */
    private $id;
    /** @var string $name */
    private $name;
    /** @var App[] */
    private $apps;

    /**
     * @param string $name Group name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return App[]
     */
    public function getApps()
    {
        return $this->apps;
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
     * @param string $name
     *
     * @throws \LogicException
     *
     * @return $this
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \LogicException('The group name must be a non-empty string');
        }

        $this->name = $name;

        return $this;
    }
}
