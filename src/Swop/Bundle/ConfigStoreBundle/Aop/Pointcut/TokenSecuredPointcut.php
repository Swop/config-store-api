<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Aop\Pointcut;

use Doctrine\Common\Annotations\Reader;
use JMS\AopBundle\Aop\PointcutInterface;

class TokenSecuredPointcut implements PointcutInterface
{
    /** @var Reader */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function matchesClass(\ReflectionClass $class)
    {
//        if ($class->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\Controller')) {
//            var_dump('sublcass!!', $class->getName());
//        }
//
//        return $class->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\Controller');
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
//        $annots = $this->reader->getMethodAnnotations($method);
//
//        if (!empty($annots)) {
//            var_dump($method->getName());
//        }
//        if ($method->getName() === 'deleteAction') {
//            die('toto');
//            var_dump(null !== $this->reader->getMethodAnnotation($method, '\Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured'));die();
//        }
//        return true;
//        var_dump($method->getName());die();
        return null !== $this->reader->getMethodAnnotation($method, 'Swop\Bundle\ConfigStoreBundle\Annotation\TokenSecured');
//        return null !== $this->reader->getMethodAnnotation($method, 'Annotation\TokenSecured');
    }
}
