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

namespace KamiYang\ProjectVersion\EventListener;

use KamiYang\ProjectVersion\Service\ProjectVersionService;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\StringUtility;

final class ProjectVersionEventListener
{
    /**
     * @var ProjectVersionService
     */
    private $projectVersionService;

    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(ProjectVersionService $projectVersionService, LanguageService $languageService)
    {
        $this->projectVersionService = $projectVersionService;
        $this->languageService = $languageService;
    }

    public function __invoke(SystemInformationToolbarCollectorEvent $event)
    {
        $projectVersion = $this->projectVersionService->getProjectVersion();
        $version = $projectVersion->getVersion();
        if (StringUtility::beginsWith($version, 'LLL:')) {
            $version = $this->languageService->sL($version);
        }

        $event->getToolbarItem()->addSystemInformation(
            $projectVersion->getTitle(),
            $version,
            $projectVersion->getIconIdentifier()
        );
    }
}
