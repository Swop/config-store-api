<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\ConfigStore\ConfigView;

use Swop\ConfigStore\Model\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigViewDumper
{
    /** @var ConfigViewAdapter[] */
    private $adapters;

    public function registerAdapter(ConfigViewAdapter $adapter)
    {
        $this->adapters[] = $adapter;
    }

    /**
     * Build the app configuration response, based on the given format, if supported
     *
     * @param string $format
     * @param App    $app
     * @param array  $additionalHeaders
     *
     * @return Response
     */
    public function forgeConfigurationResponse($format, App $app, Request $request, array $additionalHeaders = array())
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supportsFormat($format)) {
                $content     = $adapter->dump($app, $request->get('dump_options', array()));
                $contentType = $adapter->getContentType() ?: 'text/plain';

                $headers                 = $additionalHeaders;
                $headers['Content-Type'] = $contentType;

                return new Response($content, Response::HTTP_OK, $headers);
            }
        }

        throw new \RuntimeException(sprintf('The format `%s` is not supported by any ConfigView adapters.', $format));
    }

    /**
     * Checks if any adapters supports the given format
     *
     * @param string $format
     *
     * @return bool
     */
    public function supportsFormat($format)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supportsFormat($format)) {
                return true;
            }
        }

        return false;
    }
}
