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

use Doctrine\Common\Persistence\ObjectManager;
use Swop\ConfigStore\Model\App;
use Swop\ConfigStore\Model\ConfigItem;
use Swop\ConfigStore\Repository\ConfigItemRepository;

class ConfigManager
{
    /** @var ConfigItemRepository */
    protected $configItemRepository;
    /** @var ObjectManager */
    protected $persistenceManager;

    /**
     * @param ConfigItemRepository $configItemRepository
     * @param ObjectManager        $persistenceManager
     */
    public function __construct(ConfigItemRepository $configItemRepository, ObjectManager $persistenceManager)
    {
        $this->configItemRepository = $configItemRepository;
        $this->persistenceManager   = $persistenceManager;
    }

    /**
     * Gets other config items which share the same key on other apps from the same app group.
     *
     * @param ConfigItem $configItem
     *
     * @return array
     */
    public function getCompetitorConfigItems(ConfigItem $configItem)
    {
        return $this->configItemRepository->findConfigItemsFromOtherApps($configItem);
    }

    /**
     * Compute the config diff between two apps
     *
     * @param App $app1
     * @param App $app2
     *
     * @return array
     */
    public function diff(App $app1, App $app2)
    {
        $diff = [
            'app_left'      => $app1,
            'app_right'     => $app2,
            'keys_union'    => [],
            'identical'    => [],
            'different'    => [],
            'missing_left'  => [],
            'missing_right' => [],
        ];

        $configs1 = $app1->getConfigArray();
        $keys1    = array_keys($configs1);
        sort($keys1);
        $count1   = count($keys1);

        $configs2 = $app2->getConfigArray();
        $keys2    = array_keys($configs2);
        sort($keys2);
        $count2   = count($keys2);

        $diff['keys_union'] = array_keys(array_flip(array_merge($keys1, $keys2)));
        sort($diff['keys_union']);

        $index1 = $index2 = 0;

        while (true) {
            if ($index1 >= $count1 && $index2 >= $count2) {
                break;
            }

            if ($index1 >= $count1) {
                $diff['missing_left'][] = $keys2[$index2];
                $index2 += 1;

                continue;
            }

            if ($index2 >= $count2) {
                $diff['missing_right'][] = $keys1[$index1];
                $index1 += 1;

                continue;
            }

            $key1   = $keys1[$index1];
            $key2   = $keys2[$index2];

            $comparison = strcmp($key1, $key2);

            if (0 == $comparison) {
                $value1 = $configs1[$key1];
                $value2 = $configs2[$key2];

                if ($value1 === $value2) {
                    $diff['identical'][] = $key1;
                } else {
                    $diff['different'][] = $key1;
                }

                $index1 += 1;
                $index2 += 1;
            } elseif (0 > $comparison) {
                $diff['missing_right'][] = $key1;
                $index1 += 1;
            } elseif (0 < $comparison) {
                $diff['missing_left'][] = $key2;
                $index2 += 1;
            }
        }

        return $diff;
    }

    /**
     * Saves a config item
     *
     * @param ConfigItem $configItem
     */
    public function save(ConfigItem $configItem)
    {
        $this->persistenceManager->persist($configItem);
        $this->persistenceManager->flush($configItem);
    }

    /**
     * Deletes a config item
     *
     * @param ConfigItem $configItem
     */
    public function delete(ConfigItem $configItem)
    {
        $this->persistenceManager->remove($configItem);
        $this->persistenceManager->flush($configItem);
    }
}
