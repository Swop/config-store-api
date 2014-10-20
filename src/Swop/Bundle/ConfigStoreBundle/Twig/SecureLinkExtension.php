<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecureLinkExtension extends \Twig_Extension
{
    const TOKEN_REQUEST_KEY = '_token';

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UrlGeneratorInterface     $urlGenerator
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator     = $urlGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('secure_path', array($this, 'getSecuredPath')),
        );
    }

    /**
     * @param string $name       Route name
     * @param string $intention  Token intention
     * @param array  $parameters Route parameter
     * @param bool   $relative   Relative url
     *
     * @return string
     */
    public function getSecuredPath($name, $intention = 'default', $parameters = array(), $relative = false)
    {
        $parameters[self::TOKEN_REQUEST_KEY] = $this->csrfTokenManager->getToken($intention)->getValue();

        return $this->urlGenerator->generate(
            $name,
            $parameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'swop_config_store_twig_secure_link';
    }
}
