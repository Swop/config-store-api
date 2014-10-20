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

use Swop\ConfigStore\Form\DataTransformer\AppGroupToGroupIdTransformer;
use Swop\ConfigStore\Manager\AppManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AppGroupSelectorType extends AbstractType
{
    /** @var AppManager */
    private $appManager;

    /**
     * @param AppManager $appManager
     */
    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }


    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new AppGroupToGroupIdTransformer($this->appManager));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'empty_data' => null,
                'invalid_message' => 'The selected group does not exist',
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'app_group_selector';
    }
}
