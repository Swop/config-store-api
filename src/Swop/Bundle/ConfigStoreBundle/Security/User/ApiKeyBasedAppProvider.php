<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Security\User;

use Swop\ConfigStore\Exception\UnknownAppException;
use Swop\ConfigStore\Manager\AppManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyBasedAppProvider implements UserProviderInterface
{
    /** @var AppManager $appManager */
    private $appManager;

    /**
     * @param AppManager $appManager
     */
    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $apiKey = $username;

        try {
            $app = $this->appManager->getByAccessKey($apiKey);
        } catch (UnknownAppException $e) {
            throw new UsernameNotFoundException();
        }

        return $app;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        // We use stateless authentication here...
        throw new UnsupportedUserException();
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Swop\ConfigStore\Model\App';
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }
}
