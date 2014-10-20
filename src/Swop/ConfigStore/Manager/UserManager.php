<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Manager;

use Swop\ConfigStore\Exception\UnknownUserException;
use Swop\ConfigStore\Model\User;
use Swop\ConfigStore\Repository\UserRepository;

class UserManager
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $value
     *
     * @return User
     *
     * @throws UnknownUserException
     */
    public function getByUsernameOrEmail($value)
    {
        if (null === $user = $this->userRepository->findByUsernameOrEmail($value)) {
            throw new UnknownUserException($value);
        }

        return $user;
    }
}
