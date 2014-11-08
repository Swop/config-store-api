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
use Swop\ConfigStore\Model\ConfigItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured;

class AppConfigController extends Controller
{
    public function updateAction(Request $request, $appSlug)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getBySlug($appSlug);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $originalConfigItems = [];
        foreach ($app->getConfigItems() as $configItem) {
            $originalConfigItems[] = $configItem;
        }

        $form = $this->createForm("appConfig", $app);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($app->getConfigItems() as $configItem) {
                /** @var ConfigItem $toDel */
                foreach ($originalConfigItems as $key => $toDel) {
                    if ($toDel->getKey() === $configItem->getKey()) {
                        unset($originalConfigItems[$key]);
                    }
                }
            }

            /** @var ConfigItem $configItem */
            foreach ($originalConfigItems as $configItem) {
                $configItem->getApp()->removeConfigItem($configItem);
            }

            $this->get('swop.config_store.manager.config')->deleteMultiple($originalConfigItems);
            $appManager->saveApp($app);

            $context = SerializationContext::create()->setGroups('App');
            return new Response($this->get('jms_serializer')->serialize($app, 'json', $context), Response::HTTP_OK);
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
    public function editAction($appSlug)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getBySlug($appSlug);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $serializer = $this->get('jms_serializer');

        return $this->render(
            'SwopConfigStoreBundle:Admin/App:diff2.html.twig',
            [
                'application' => $serializer->serialize($app, 'json', SerializationContext::create()->setGroups('App'))
            ]
        );
    }
}
