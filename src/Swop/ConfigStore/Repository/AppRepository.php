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
use Doctrine\ORM\Query;
use Swop\ConfigStore\Model\App;

class AppRepository extends EntityRepository
{
    /**
     * Find an app which have the given slug
     *
     * @param string $appSlug
     *
     * @return App|null
     */
    public function findBySlug($appSlug)
    {
        return $this->queryApp()
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $appSlug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find an app which have the given access key
     *
     * @param string $accessKey
     *
     * @return App|null
     */
    public function findByAccessKey($accessKey)
    {
        return $this->queryApp()
            ->andWhere('a.accessKey = :accessKey')
            ->setParameter('accessKey', $accessKey)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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
     * Checks if the given access key is attached to an App
     *
     * @param string $accessKey
     *
     * @return bool
     */
    public function isValidAccessKey($accessKey)
    {
        $dql = "SELECT COUNT(a.id) FROM ".$this->getEntityName()." a
                WHERE a.accessKey = :accessKey";

        $count = $this->_em->createQuery($dql)
            ->setParameter('accessKey', $accessKey)
            ->getResult(Query::HYDRATE_SINGLE_SCALAR);

        return (0 < (int)$count);
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
            ->leftJoin('a.group', 'g')
        ;
    }
}
