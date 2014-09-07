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

use Behat\Gherkin\Node\PyStringNode;
use Behat\WebApiExtension\Context\WebApiContext;

class VerboseWebApiContext extends WebApiContext
{
    /**
     * {@inheritDoc}
     */
    public function theResponseCodeShouldBe($code)
    {
        try {
            parent::theResponseCodeShouldBe($code);
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $this->printResponse();
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        try {
            parent::theResponseShouldContainJson($jsonString);
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $this->printResponse();
            throw $e;
        }
    }
}
