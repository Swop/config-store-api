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
use Swop\ConfigStore\Model\ConfigItem;

class ConfigItemRepository extends EntityRepository
{
    public function findConfigItemsFromOtherApps(ConfigItem $configItem)
    {
        return $this->createQueryBuilder('ci')
            ->leftJoin('ci.app', 'a')
            ->where('a.group = :appGroup')
            ->andWhere('a <> :app')
            ->andWhere('ci.key = :key')
            ->getQuery()
            ->setParameter('appGroup', $configItem->getApp()->getGroup())
            ->setParameter('app', $configItem->getApp())
            ->setParameter('key', $configItem->getKey())
            ->getResult();
    }
}
