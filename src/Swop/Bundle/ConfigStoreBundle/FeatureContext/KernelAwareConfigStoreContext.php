<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\FeatureContext;

use Behat\Symfony2Extension\Context\KernelDictionary;
use Swop\ConfigStore\Features\Context\ConfigStoreFeatureContext as BaseConfigStoreContext;

class KernelAwareConfigStoreContext extends BaseConfigStoreContext
{
    use KernelDictionary;

    /**
     * {@inheritDoc}
     */
    protected function getAppManager()
    {
        return $this->getContainer()->get('swop.config_store.manager.app');
    }

    /**
     * {@inheritDoc}
     */
    protected function getObjectManager($managerName = null)
    {
        return $this->getContainer()->get('doctrine')->getManager($managerName);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDatabaseConnection($name)
    {
        return $this->getContainer()->get('doctrine')->getConnection($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDatabaseConnections()
    {
        return $this->getContainer()->get('doctrine')->getConnections();
    }
}
