<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Exception;

use Swop\ConfigStore\Model\App;
use Swop\ConfigStore\Model\ConfigItem;

class AlreadyExistingConfigKeyException extends \RuntimeException
{
    /** @var ConfigItem $configItem */
    protected $configItem;
    /** @var string $application */
    protected $application;

    /**
     * @param ConfigItem $configItem
     * @param App        $application
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct(ConfigItem $configItem, App $application, $code = 0, \Exception $previous = null)
    {
        $message = sprintf(
            "The application %s already have the config key %s",
            $application->getName(),
            $configItem->getKey()
        );

        parent::__construct($message, $code, $previous);

        $this->configItem  = $configItem;
        $this->application = $application;
    }

    /**
     * @return App
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return ConfigItem
     */
    public function getConfigItem()
    {
        return $this->configItem;
    }
}
