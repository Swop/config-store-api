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
use Swop\ConfigStore\ConfigView\ConfigViewDumper;
use Swop\ConfigStore\Model\App;
use Symfony\Component\HttpFoundation\Request;

class ConfigViewHandler extends ViewHandler
{
    /** @var ConfigViewDumper $configViewDumper */
    private $configViewDumper;

    /**
     * @param ConfigViewDumper $dumper
     */
    public function setConfigViewDumper(ConfigViewDumper $dumper)
    {
        $this->configViewDumper = $dumper;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(View $view, Request $request = null)
    {
        if (null === $request) {
            $request = $this->container->get('request');
        }

        $format = $view->getFormat() ?: $request->getRequestFormat();

        if (!$view->getData() instanceof App || !$this->configViewDumper->supportsFormat($format)) {
            return parent::handle($view, $request);
        }

        return $this->configViewDumper->forgeConfigurationResponse($format, $view->getData(), $request, $view->getHeaders());
    }
}
