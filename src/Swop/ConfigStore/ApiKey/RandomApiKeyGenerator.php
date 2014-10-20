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

use Symfony\Component\Security\Core\Util\SecureRandomInterface;

class RandomApiKeyGenerator implements ApiKeyGenerator
{
    /** @var SecureRandomInterface */
    private $secureRandom;

    /**
     * @param SecureRandomInterface $secureRandom
     */
    public function  __construct(SecureRandomInterface $secureRandom)
    {
        $this->secureRandom = $secureRandom;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return rtrim(strtr(base64_encode($this->secureRandom->nextBytes(50)), '+/', '-_'), '=');
    }
}
