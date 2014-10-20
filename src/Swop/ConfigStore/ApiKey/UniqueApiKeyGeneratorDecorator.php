<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\ApiKey;

use Swop\ConfigStore\Manager\AppManager;

class UniqueApiKeyGeneratorDecorator implements ApiKeyGenerator
{
    /** @var AppManager */
    private $appManager;
    /** @var ApiKeyGenerator */
    private $wrapped;

    /**
     * @param AppManager      $appManager
     * @param ApiKeyGenerator $wrapped
     */
    public function __construct(AppManager $appManager, ApiKeyGenerator $wrapped)
    {
        $this->appManager = $appManager;
        $this->wrapped    = $wrapped;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        do {
            $generatedAccessKey = $this->wrapped->generate();
        } while ($this->appManager->isValidAccessKey($generatedAccessKey));

        return $generatedAccessKey;
    }
}
