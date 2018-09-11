<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Facade;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

/**
 * Class SystemEnvironmentBuilderFacade
 * Only used for mocking purposes. This is a wrapper for the actual Utility.
 *
 * @see \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder
 * @internal
 * @package KamiYang\ProjectVersion\Facade
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
class SystemEnvironmentBuilderFacade
{
    /**
     * Check if the given function is disabled in the system
     *
     * @param string $function
     * @return bool
     */
    public function isFunctionDisabled($function): bool
    {
        return SystemEnvironmentBuilder::isFunctionDisabled($function);
    }
}
