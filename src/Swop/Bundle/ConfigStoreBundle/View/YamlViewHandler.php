<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\View;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Swop\ConfigStore\Model\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class YamlViewHandler extends ViewHandler
{
    /**
     * @param \FOS\RestBundle\View\ViewHandler $handler
     * @param View                             $view
     * @param Request                          $request
     * @param string                           $format
     *
     * @throws \RuntimeException
     *
     * @return Response
     */
    public function createYamlResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        if ($format !== 'yaml') {
            throw new \RuntimeException("The YamlVIewHandler only supports 'yaml' format.");
        }

        $data = $view->getData();

        // The Yaml View Handler is only used to dump app configurations
        if (!$data instanceof App) {
            $view->setFormat('json');
            return $handler->handle($view, $request);
        }

        $content = Yaml::dump(["parameters" => $data->getConfigArray()]);

        return new Response($content, 200, $view->getHeaders());
    }
}
