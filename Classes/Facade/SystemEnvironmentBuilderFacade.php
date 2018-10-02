<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Facade;

/*
 * This file is part of the ProjectVersion project.
 *
 * It is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

/**
 * Class SystemEnvironmentBuilderFacade
 * Only used for mocking purposes. This is a wrapper for the actual Utility.
 *
 * @see \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder
 * @internal
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
