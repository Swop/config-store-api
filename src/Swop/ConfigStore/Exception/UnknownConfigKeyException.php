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

class UnknownConfigKeyException extends \RuntimeException
{
    /** @var string $configKey */
    protected $configKey;
    /** @var App $application */
    protected $application;

    /**
     * @param string     $configKey
     * @param App|null   $application
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($configKey, App $application = null, $code = 0, \Exception $previous = null)
    {
        if (null === $application) {
            $message = sprintf("Unknown config key %s", $configKey);
        } else {
            $message = sprintf(
                "The application %s doesn't have the config key %s",
                $application->getName(),
                $configKey
            );
        }

        parent::__construct($message, $code, $previous);

        $this->configKey   = $configKey;
        $this->application = $application;
    }

    /**
     * @return App|null
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return $this->configKey;
    }
}
