<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Annotation;

/**
 * @Annotation
 */
class TokenSecured
{
    /** Default intention */
    const DEFAULT_INTENTION = 'default';

    /** @var string */
    private $intention;

    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['intention'] = $options['value'];
            unset($options['value']);
        }

        if (!isset($options['intention'])) {
            $options['intention'] = self::DEFAULT_INTENTION;
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getIntention()
    {
        return $this->intention;
    }
}
