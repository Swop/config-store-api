<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\ConfigStore\ConfigView\Adapter;

use JMS\Serializer\SerializerInterface;
use Swop\ConfigStore\ConfigView\ConfigViewAdapter;
use Swop\ConfigStore\Model\App;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

class JsonConfigView implements ConfigViewAdapter
{
    /** @var SerializerInterface $serializer */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsFormat($format)
    {
        return 'json' === $format;
    }

    /**
     * {@inheritDoc}
     */
    public function dump(App $app, array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $options = $resolver->resolve($options);

        if (null === $options['rootNode']) {
            $data = $app->getConfigArray();
        } else {
            $data = array($options['rootNode'] => $app->getConfigArray());
        }

        return $this->serializer->serialize($data, 'json');
    }

    /**
     * {@inheritDoc}
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    private function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(
            array(
                'rootNode'
            )
        );

        $resolver->setDefaults(
            array(
                'rootNode' => null
            )
        );

        $resolver->setAllowedTypes(
            array(
                'rootNode' => array('null', 'string')
            )
        );
    }
}
