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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

class PhpConfigView implements ConfigViewAdapter
{
    /**
     * {@inheritDoc}
     */
    public function supportsFormat($format)
    {
        return 'php' === $format;
    }

    /**
     * {@inheritDoc}
     */
    public function dump(App $app, array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $options = $resolver->resolve($options);

        $serializedContent = serialize($app->getConfigArray());

        if (!$options['useDefineStatements']) {
            $tpl = <<< EOF
<?php
return unserialize('%s');
EOF;
        } else {
            $tpl = <<< EOF
<?php
\$config = unserialize('%s');

foreach (\$config as \$key => \$value) {
    if (!defined(\$key)) {
        define(\$key, \$value);
    }
}

return \$config;
EOF;
        }

        return sprintf($tpl, $serializedContent);
    }

    /**
     * {@inheritDoc}
     */
    public function getContentType()
    {
        return 'application/x-php';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    private function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(
            array(
                'useDefineStatements'
            )
        );

        $resolver->setDefaults(
            array(
                'useDefineStatements' => false
            )
        );

        $resolver->setAllowedTypes(
            array(
                'useDefineStatements' => array('bool')
            )
        );

        $resolver->setNormalizers(
            array(
                'useDefineStatements' => function (Options $options, $value) {
                    return (bool)$value;
                },
            )
        );
    }
}
