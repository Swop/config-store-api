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
use Swop\ConfigStore\Model\App;
use Swop\ConfigStore\Model\ConfigItem;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class MyconfigController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets all the authenticated application config items
     *
     * @return App
     *
     * @Rest\View(serializerGroups="App")
     */
    public function getAction()
    {
        $app        = $this->get('security.context')->getToken()->getUser();
        $appManager = $appManager = $this->get('swop.config_store.manager.app');

        // Check coherence with the reference app
        if (null !== $referenceApp = $appManager->getReferenceApp($app)) {
            $configManager = $this->get('swop.config_store.manager.config');
            $diff = $configManager->diff($app, $referenceApp);

            if (!$configManager->isEmptyKeyDiff($diff)) {
                throw new ConflictHttpException(
                    'The configuration doesn\'t match with the reference. Please update the config before using it in your program.'
                );
            }
        }

        return $app;
    }
}
