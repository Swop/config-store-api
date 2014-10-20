<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\ConfigStore\ConfigView;

use Swop\ConfigStore\Model\App;

interface ConfigViewAdapter
{
    /**
     * Checks if the apadter supports the given format
     *
     * @param string $format
     *
     * @return bool
     */
    public function supportsFormat($format);

    /**
     * Dump the app configuration to the required format string
     *
     * @param App   $app
     * @param array $options
     *
     * @return string
     */
    public function dump(App $app, array $options = array());

    /**
     * Get the content-type to associate to the response
     *
     * @return string
     */
    public function getContentType();
}
