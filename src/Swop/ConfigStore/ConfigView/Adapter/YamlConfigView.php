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

use Swop\ConfigStore\ConfigView\ConfigViewAdapter;
use Swop\ConfigStore\Model\App;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

class YamlConfigView implements ConfigViewAdapter
{
    /**
     * {@inheritDoc}
     */
    public function supportsFormat($format)
    {
        return 'yaml' === $format;
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
            $content = Yaml::dump($app->getConfigArray());
        } else {
            $content = Yaml::dump(array($options['rootNode'] => $app->getConfigArray()));
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getContentType()
    {
        return 'application/x-yaml';
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
                'rootNode' => 'parameters'
            )
        );

        $resolver->setAllowedTypes(
            array(
                'rootNode' => array('null', 'string')
            )
        );
    }
}
