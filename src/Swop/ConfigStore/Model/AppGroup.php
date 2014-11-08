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

class AppGroup
{
    /** @var int $id */
    private $id;
    /** @var string $name */
    private $name;
    /** @var Collection */
    private $apps;
    /** @var App */
    private $reference;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apps = new ArrayCollection();
    }

    /**
     * @return App[]
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * @param App $app
     */
    public function addApp(App $app)
    {
        if (!$this->apps->contains($app)) {
            $this->apps->add($app);
        }
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

    /**
     * @param App $app
     */
    public function setReference(App $app)
    {
        if ($app->getGroup() !== $this) {
            throw new \DomainException('An application must be part of the group if you want to use it as a reference for the group');
        }

        $this->reference = $app;
    }

    /**
     * Gets the reference attribute
     *
     * @return App
     */
    public function getReference()
    {
        return $this->reference;
    }
}
