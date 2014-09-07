<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Swop\ConfigStore\Model\App;
use Swop\ConfigStore\Model\AppGroup;
use Swop\ConfigStore\Model\ConfigItem;

abstract class ConfigStoreFeatureContext implements Context, SnippetAcceptingContext
{
    use DatabaseContextTrait;

    /**
     * @BeforeScenario
     */
    public function setupScenario($event)
    {
        $this->resetDatabase();
    }

    /**
     * @Given the following groups:
     */
    public function setupGroups(TableNode $groupTable)
    {
        $appManager = $this->getAppManager();

        foreach ($groupTable->getHash() as $groupHash) {
            if (isset($groupHash['name'])) {
                $groupName = $groupHash['name'];
            } else {
                throw new \LogicException('You must declare a group name');
            }

            $group = new AppGroup($groupName);

            $appManager->saveGroup($group);
        }
    }

    /**
     * @Given the following apps:
     */
    public function setupApps(TableNode $appTable)
    {
        $appManager = $this->getAppManager();

        foreach ($appTable->getHash() as $appHash) {
            if (isset($appHash['name'])) {
                $appName = $appHash['name'];
            } else {
                throw new \LogicException('You must declare an app name');
            }

            $apiKey = isset($appHash['api_key']) ? $appHash['api_key'] : uniqid(time());

            $app = new App($appName, $apiKey);

            if (isset($appHash['description']) && 'null' !== $appHash['description']) {
                $app->setDescription($appHash['description']);
            }

            if (isset($appHash['group_id']) && 'null' !== $appHash['group_id']) {
                $app->setGroup($appManager->getGroup($appHash['group_id']));
            }

            $appManager->saveApp($app);

            if (isset($appHash['config_items'])) {
                if ($configItemsHash = @json_decode($appHash['config_items'], true)) {
                    foreach ($configItemsHash as $key => $value) {
                        $app->addConfigItem(new ConfigItem($app, $key, $value));
                    }
                }
            }

            $appManager->saveApp($app);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function isConnectionReadOnly($connexionName)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function isTableReadOnly($connexionName, $tableName)
    {
        return false;
    }

    /**
     * @return \Swop\ConfigStore\Manager\AppManager
     */
    abstract protected function getAppManager();
}
