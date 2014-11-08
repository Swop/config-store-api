<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Form\Type;

use Swop\ConfigStore\ApiKey\ApiKeyGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AppConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configItems', 'collection', [
                    'type'         => 'configItem',
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'prototype'    => true,
                    'by_reference' => false,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'      => 'Swop\ConfigStore\Model\App',
                'csrf_protection' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appConfig';
    }
}
