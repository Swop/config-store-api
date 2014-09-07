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
use Swop\ConfigStore\Exception\UnknownAppException;
use Swop\ConfigStore\Model\App;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AppController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an app
     *
     * @param string $appName Name of the app
     *
     * @return App
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\View(serializerGroups="App")
     */
    public function getAction($appName)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $app;
    }

    /**
     * Gets all apps
     *
     * @return array
     *
     * @Rest\View(serializerGroups="App")
     */
    public function cgetAction()
    {
        $appManager = $this->get('swop.config_store.manager.app');

        return $appManager->all();
    }

    /**
     * Perform a diff between configuration of the given two apps
     *
     * @param string $appName
     * @param string $otherAppName
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Rest\Get("/apps/{appName}/diff/{otherAppName}")
     * @Rest\View(serializerGroups="Diff")
     */
    public function diffAction($appName, $otherAppName)
    {
        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
            $otherApp = $appManager->getByName($otherAppName);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        if ($app->getGroup() !== $otherApp->getGroup()) {
            throw new BadRequestHttpException("The too apps aren't from the same group. Thus it can't be compared.");
        }

        $configManager = $this->get('swop.config_store.manager.config');

        $diff = $configManager->diff($app, $otherApp);

        return $diff;
    }

    /**
     * Temp method: Gets a visual output of a diff action
     *
     * @param string $appName
     * @param string $otherAppName
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function htmlDiffAction($appName, $otherAppName)
    {
        $content = '';

        $appManager = $this->get('swop.config_store.manager.app');

        try {
            $app = $appManager->getByName($appName);
            $otherApp = $appManager->getByName($otherAppName);
        } catch (UnknownAppException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        if ($app->getGroup() !== $otherApp->getGroup()) {
            throw new BadRequestHttpException("The too apps aren't from the same group. Thus it can't be compared.");
        }

        $configManager = $this->get('swop.config_store.manager.config');

        $diff = $configManager->diff($app, $otherApp);

        $content .= "<table>";
        $content .= '<thead><th style="width: 200px;">'.$app->getName().'</th><th style="width: 200px;">'.$otherApp->getName().'</th></thead>';
        $content .= '<tbody>';
        foreach($diff['keys_union'] as $key) {
            $content .= '<tr>';
            if (in_array($key, $diff['missing_left'], true)) {
                $content .= '<td></td><td style="background-color: green">'.$key.': '.$otherApp->getConfigItem($key)->getValue().'</td>';
            } elseif (in_array($key, $diff['missing_right'], true)) {
                $content .= '<td style="background-color: green">'.$key.': '.$app->getConfigItem($key)->getValue().'</td><td></td>';
            } elseif (in_array($key, $diff['identical'], true)) {
                $content .= '<td>'.$key.': '.$app->getConfigItem($key)->getValue().'</td><td>'.$key.': '.$otherApp->getConfigItem($key)->getValue().'</td>';
            } elseif (in_array($key, $diff['different'], true)) {
                $content .= '<td style="background-color: red">'.$key.': '.$app->getConfigItem($key)->getValue().'</td><td style="background-color: red">'.$key.': '.$otherApp->getConfigItem($key)->getValue().'</td>';
            }
            $content .= '</tr>';
        }
        $content .= '</tbody>';
        $content .= '</table>';

        return new Response($content);
    }
}
