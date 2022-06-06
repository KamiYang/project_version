<?php

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

defined('TYPO3_MODE') or die();

(static function () {
    if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_BE) {
        //Fetch ExtensionConfiguration
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \KamiYang\ProjectVersion\Configuration\ExtensionConfiguration::class
        );

        // Register custom icon
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)
            ->registerIcon(
                'information-project-version',
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:project_version/Resources/Public/Icons/ToolbarItem.svg'
                ]
            );
    }
})();
