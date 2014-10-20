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

use Swop\ConfigStore\Manager\AppManager;
use Swop\ConfigStore\Model\App;

class DiffWithReferenceExtension extends \Twig_Extension
{
    /** @var AppManager $appManager */
    private $appManager;

    /**
     * @param AppManager $appManager
     */
    public function __construct(AppManager $appManager)
    {
        $this->appManager    = $appManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('diff_with_ref', array($this, 'getDiffWithReference')),
            new \Twig_SimpleFunction('is_out_of_sync', array($this, 'isOutOfSyncWithRef')),
        );
    }

    /**
     * @param App $app
     *
     * @return array|null
     */
    public function getDiffWithReference(App $app)
    {
        return $this->appManager->getDiffWithReference($app);
    }

    /**
     * Returns if the
     * @param App $app
     *
     * @return bool
     */
    public function isOutOfSyncWithRef(App $app)
    {
        return $this->appManager->isOutOfSyncWithReference($app);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'swop_config_store_twig_diff_with_reference';
    }
}
