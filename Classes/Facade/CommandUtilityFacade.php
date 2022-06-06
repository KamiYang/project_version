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

class CommandUtilityFacade
{
    /**
     * Wrapper function for php exec function
     * Needs to be central to have better control and possible fix for issues
     *
     * @param string $command
     * @param array|null $output
     * @param int $returnValue
     * @return string
     */
    public function exec($command, &$output = null, &$returnValue = 0)
    {
        return CommandUtility::exec($command, $output, $returnValue);
    }
}
