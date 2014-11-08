<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Controller\Admin;

use JMS\Serializer\SerializationContext;
use Swop\ConfigStore\Exception\UnknownGroupException;
use Swop\ConfigStore\Model\AppGroup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured;

class GroupController extends Controller
{
    public function createAction(Request $request)
    {
        $group = new AppGroup();

        $form = $this->createForm("appGroup", $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swop.config_store.manager.app')->saveGroup($group);

            $context = SerializationContext::create()->setGroups('AppGroup');
            return new Response($this->get('jms_serializer')->serialize($group, 'json', $context), Response::HTTP_CREATED);
        }

        return $form;
    }

    /**
     * @TokenSecured("deleteGroup")
     * @param Request $request
     * @param string  $groupId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $groupId)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $group = $appManager->getGroup($groupId);
        } catch (UnknownGroupException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $appManager->deleteGroup($group);

        $request->getSession()->getFlashBag()->add('success', 'The app '. $group->getName() . ' is now deleted.');

        return $this->redirect($this->generateUrl('admin_app_list'));
    }
}
