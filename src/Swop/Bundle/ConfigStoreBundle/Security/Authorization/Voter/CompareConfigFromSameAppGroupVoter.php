<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Swop\ConfigStore\Manager\AppManager;
use Swop\ConfigStore\Model\App;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CompareConfigFromSameAppGroupVoter implements VoterInterface
{
    const COMPARE_CONFIG = 'compare_config';

    /** @var AppManager */
    private $appManager;

    /**
     * @param AppManager $appManager
     */
    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return self::COMPARE_CONFIG === $attribute;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Swop\ConfigStore\Model\App';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $currentApp = $token->getUser();

        if (!$currentApp instanceof App) {
            return VoterInterface::ACCESS_DENIED;
        }

        $currentAppGroup = $currentApp->getGroup();
        $targetAppGroup  = $object->getGroup();

        if ($currentAppGroup === $targetAppGroup) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
