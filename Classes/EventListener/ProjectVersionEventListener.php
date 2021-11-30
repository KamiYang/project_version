<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\EventListener;

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
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

final class ProjectVersionEventListener
{
    private $projectVersionService;

    public function __construct(ProjectVersionService $projectVersionService)
    {
        $this->projectVersionService = $projectVersionService;
    }

    public function __invoke(SystemInformationToolbarCollectorEvent $event)
    {
        $projectVersion = $this->projectVersionService->getProjectVersion();

        $version = $projectVersion->getVersion();

        if (StringUtility::beginsWith($version, 'LLL:')) {
            $version = GeneralUtility::makeInstance(LocalizationUtilityFacade::class)->translate($version);
        }

        $event->getToolbarItem()->addSystemInformation(
            $projectVersion->getTitle(),
            $version,
            $projectVersion->getIconIdentifier()
        );
    }
}
