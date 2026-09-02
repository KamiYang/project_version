<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Backend\EventListener;

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

use KamiYang\ProjectVersion\Service\ProjectVersionService;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

/**
 * Adds the project version to the "System Information" toolbar dropdown in the TYPO3 backend.
 */
#[AsEventListener(identifier: 'project-version/system-information')]
final readonly class SystemInformationListener
{
    public function __construct(
        private ProjectVersionService $projectVersionService,
        private LanguageServiceFactory $languageServiceFactory,
    ) {}

    public function __invoke(SystemInformationToolbarCollectorEvent $event): void
    {
        $projectVersion = $this->projectVersionService->getProjectVersion();
        $languageService = $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER'] ?? null);

        $event->getToolbarItem()->addSystemInformation(
            $languageService->sL($projectVersion->getTitle()),
            $languageService->sL($projectVersion->getVersion()),
            $projectVersion->getIconIdentifier(),
        );
    }
}
