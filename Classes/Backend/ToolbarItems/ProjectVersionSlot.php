<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Backend\ToolbarItems;

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

use KamiYang\ProjectVersion\Facade\LocalizationUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProjectVersionSlot
 */
final class ProjectVersionSlot implements SingletonInterface
{
    /**
     * @param \TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem $pObj
     */
    public function getProjectVersion(SystemInformationToolbarItem $pObj)
    {
        $projectVersion = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ProjectVersionService::class)
            ->getProjectVersion();

        $version = $projectVersion->getVersion();

        if (StringUtility::beginsWith($version, 'LLL:')) {
            $version = GeneralUtility::makeInstance(LocalizationUtilityFacade::class)->translate($version);
        }

        $pObj->addSystemInformation(
            $projectVersion->getTitle(),
            $version,
            $projectVersion->getIconIdentifier()
        );
    }
}
