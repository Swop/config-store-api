<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByUsernameOrEmail($value)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :value')
            ->orWhere('u.email = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
