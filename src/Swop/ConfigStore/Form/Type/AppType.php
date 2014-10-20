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

class AppType extends AbstractType
{
    /** @var ApiKeyGenerator */
    private $apiKeyGenerator;

    /**
     * @param ApiKeyGenerator $apiKeyGenerator
     */
    public function  __construct(ApiKeyGenerator $apiKeyGenerator)
    {
        $this->apiKeyGenerator = $apiKeyGenerator;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('accessKey', 'text')
            ->add('group', 'app_group_selector')
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $method = strtoupper($form->getConfig()->getMethod());

                if ('POST' === $method) {
                    if (!array_key_exists('accessKey', $data)) {
                        $data['accessKey'] = $this->apiKeyGenerator->generate();
                    }
                }

                $event->setData($data);
        });
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
        return 'app';
    }
}
