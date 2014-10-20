<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Aop\Interceptor;

use Doctrine\Common\Annotations\Reader;
use Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use CG\Proxy\MethodInvocation;
use CG\Proxy\MethodInterceptorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TokenSecuredInterceptor implements MethodInterceptorInterface
{
    const TOKEN_REQUEST_KEY = '_token';

    /** @var Reader */
    private $reader;
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var RequestStack */
    private $requestStack;

    /**
     * @param Reader                    $reader
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param RequestStack              $requestStack
     */
    public function __construct(Reader $reader, CsrfTokenManagerInterface $csrfTokenManager, RequestStack $requestStack)
    {
        $this->reader           = $reader;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->requestStack     = $requestStack;
    }

    public function intercept(MethodInvocation $invocation)
    {
        $request = $this->requestStack->getMasterRequest();

        if (null !== $request) {
            $reflexion = $invocation->reflection;

            /** @var TokenSecured $annotation */
            $annotation = $this->reader->getMethodAnnotation($reflexion, '\Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured');

            $providedToken = new CsrfToken($annotation->getIntention(), $request->get(self::TOKEN_REQUEST_KEY));

            if (!$this->csrfTokenManager->isTokenValid($providedToken)) {
                throw new AccessDeniedHttpException('Invalid CSRF token');
            }
        }

        return $invocation->proceed();
    }
}
