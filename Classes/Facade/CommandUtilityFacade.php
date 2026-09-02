<?php

declare(strict_types=1);

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

namespace KamiYang\ProjectVersion\Facade;

use TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * Thin wrapper around TYPO3's CommandUtility so shell access can be mocked in tests.
 *
 * @internal
 */
class CommandUtilityFacade
{
    /**
     * Execute a shell command and return the last line of its output.
     *
     * @param array<mixed>|null $output
     */
    public function exec(string $command, ?array &$output = null, int &$returnValue = 0): string
    {
        $result = CommandUtility::exec($command, $output, $returnValue);

        return $result === false ? '' : $result;
    }

    /**
     * Check whether PHP's exec() function is usable in this environment.
     */
    public function isExecAvailable(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $disabled = array_map(trim(...), explode(',', (string)ini_get('disable_functions')));

        return !in_array('exec', $disabled, true);
    }
}
