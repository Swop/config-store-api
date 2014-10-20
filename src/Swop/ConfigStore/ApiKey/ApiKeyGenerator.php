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

interface ApiKeyGenerator
{
    /**
     * Generate an api key
     *
     * @return string
     */
    public function generate();
}
