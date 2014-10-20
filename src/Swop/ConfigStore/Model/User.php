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

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /** @var  int $id */
    private  $id;
    /** @var string $name */
    private $name;
    /** @var string $email */
    private $email;
    /** @var string $username */
    private $username;
    /** @var string $slug */
    private $slug;
    /** @var string $password */
    private $password;
    /** @var string $salt */
    private $salt;
    /** @var string $rawPassword */
    private $rawPassword;

    /**
     * {inheritDoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {inheritDoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {inheritDoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {inheritDoc}
     */
    public function eraseCredentials()
    {
        $this->rawPassword = null;
    }

    /**
     * Gets the email attribute
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gets the id attribute
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the name attribute
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the slug attribute
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
