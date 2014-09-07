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
use Swop\ConfigStore\Exception\UnknownGroupException;
use Swop\ConfigStore\Model\AppGroup;

class GroupController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets a group
     *
     * @param int $groupId Id of the group
     *
     * @return AppGroup
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\View(serializerGroups="AppGroup")
     *
     */
    public function getAction($groupId)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $group = $appManager->getGroup($groupId);
        } catch (UnknownGroupException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $group;
    }

    /**
     * Gets all groups
     *
     * @Rest\View(serializerGroups="AppGroup")
     *
     * @return AppGroup
     */
    public function cgetAction()
    {
        $appManager = $this->get('swop.config_store.manager.app');

        $groups = $appManager->allGroups();

        return $groups;
    }
}
