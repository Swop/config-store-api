<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swop\ConfigStore\Exception\UnknownAppException;
use Swop\ConfigStore\Exception\UnknownConfigKeyException;
use Swop\ConfigStore\Model\ConfigItem;

class ConfigController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets a config item
     *
     * @param string $appName   Name of the app
     * @param string $configKey Config key
     *
     * @return ConfigItem
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\View(serializerGroups="ConfigItem")
     */
    public function getAction($appName, $configKey)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
            $configItem = $app->getConfigItem($configKey);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        } catch (UnknownConfigKeyException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $configItem;
    }

    /**
     * Gets all application config item
     *
     * @param string $appName Name of the app
     *
     * @return ConfigItem
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\View(serializerGroups="ConfigItem")
     */
    public function cgetAction($appName)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $app->getConfigItems();
    }

    /**
     * Gets key-like other apps configs
     *
     * @param string $appName
     * @param string $configKey
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\Get()
     * @Rest\View(serializerGroups="ConfigItem")
     */
    public function diffAction($appName, $configKey)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
            $configItem = $app->getConfigItem($configKey);

            $configManager = $this->get('swop.config_store.manager.config');
            $competitorsConfigItems = $configManager->getCompetitorConfigItems($configItem);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        } catch (UnknownConfigKeyException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return ['reference' => $configItem, 'competitors' => $competitorsConfigItems];
    }
}
