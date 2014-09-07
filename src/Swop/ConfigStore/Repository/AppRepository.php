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
use Swop\ConfigStore\Model\App;

class AppRepository extends EntityRepository
{
    /**
     * Find an app which have the given name
     *
     * @param string $appName
     *
     * @return App|null
     */
    public function findAppByName($appName)
    {
        return $this->queryApp()
            ->andWhere('a.name = :name')
            ->setParameter('name', $appName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find an app which have the given access key
     *
     * @param string $accessKey
     *
     * @return App|null
     */
    public function findAppByAccessKey($accessKey)
    {
        return $this->queryApp()
            ->andWhere('a.accessKey = :accessKey')
            ->setParameter('accessKey', $accessKey)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all apps
     *
     * @return array
     */
    public function findAllApps()
    {
        return $this->queryApp()->orderBy('a.id')->getQuery()->getResult();
    }

    /**
     * Build base query builder to fetch an app with its config values
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function queryApp()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('ci')
            ->addSelect('g')
            ->leftJoin('a.configItems', 'ci')
            ->leftJoin('a.group', 'g');
    }
}
