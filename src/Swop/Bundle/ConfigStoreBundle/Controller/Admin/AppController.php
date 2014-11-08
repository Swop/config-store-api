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
use Swop\ConfigStore\Exception\UnknownAppException;
use Swop\ConfigStore\Model\App;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured;

class AppController extends Controller
{
    /**
     * @Template()
     */
    public function listAction()
    {
        $appManager = $this->get('swop.config_store.manager.app');

        $groups = $appManager->allGroups();
        $standaloneApps = $appManager->getStandaloneApps();

//        $apps = $appManager->all();
//
//        $groups = array();
//        $standaloneApps = array();
//        /** @var App $app */
//        foreach ($apps as $app) {
//            if (null !== $group = $app->getGroup()) {
//                if (!array_key_exists($group->getId(), $groups)) {
//                    $groups[$group->getId()] = array(
//                        'group' => $group,
//                        'apps' => array($app)
//                    );
//                } else {
//                    if ($app->isRef()) {
//                        array_unshift($groups[$group->getId()]['apps'], $app);
//                    } else {
//                        $groups[$group->getId()]['apps'][] = $app;
//                    }
//                }
//            } else {
//                $standaloneApps[] = $app;
//            }
//        }
//
        return ['groups' => $groups, 'standaloneApps' => $standaloneApps];
    }

    /**
     * @TokenSecured("deleteApp")
     */
    public function deleteAction(Request $request, $appSlug)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getBySlug($appSlug);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $appManager->deleteApp($app);

        $request->getSession()->getFlashBag()->add('success', 'The app '. $app->getName() . ' is now deleted.');

        return $this->redirect($this->generateUrl('admin_app_list'));
    }

    public function createAction(Request $request)
    {
        $app = new App();

        $form = $this->createForm("app", $app);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swop.config_store.manager.app')->saveApp($app);

            $context = SerializationContext::create()->setGroups('App');
            return new Response($this->get('jms_serializer')->serialize($app, 'json', $context), Response::HTTP_CREATED);
        }

        return $form;
    }

    /**
     * Perform a diff between configuration of the given two apps
     *
     * @param string $appSlug
     * @param string $otherAppSlug
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function diffAction($appSlug, $otherAppSlug)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getBySlug($appSlug);
            $otherApp = $appManager->getBySlug($otherAppSlug);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        if ($app->getGroup() !== $otherApp->getGroup()) {
            throw new BadRequestHttpException("The too apps aren't from the same group. Thus it can't be compared.");
        }

        $configManager = $this->get('swop.config_store.manager.config');

        $diff = $configManager->diff($app, $otherApp);

        $serializer = $this->get('jms_serializer');

//        return ['diff' => $diff, 'application' => $app, 'otherApplication' => $otherApp];
        return $this->render('SwopConfigStoreBundle:Admin/App:diff2.html.twig', [
                'diff'             => $serializer->serialize($diff, 'json'),
                'application'      => $serializer->serialize($app, 'json', SerializationContext::create()->setGroups('App')),
                'otherApplication' => $serializer->serialize($otherApp, 'json', SerializationContext::create()->setGroups('App'))
        ]);
    }
}
