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

namespace KamiYang\ProjectVersion\Tests\Unit\Backend\EventListener;

use KamiYang\ProjectVersion\Backend\EventListener\SystemInformationListener;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SystemInformationListenerTest extends UnitTestCase
{
    #[Test]
    public function addsTranslatedProjectVersionToToolbarItem(): void
    {
        $projectVersion = new ProjectVersion();
        $projectVersion->setVersion('9000-rc.69');
        $projectVersion->setIconIdentifier('information-git');

        $service = self::createStub(ProjectVersionService::class);
        $service->method('getProjectVersion')->willReturn($projectVersion);

        $languageService = self::createStub(LanguageService::class);
        $languageService->method('sL')->willReturnCallback(
            static fn(string $input): string => $input === ProjectVersion::DEFAULT_TITLE ? 'Project Version' : $input
        );
        $languageServiceFactory = self::createStub(LanguageServiceFactory::class);
        $languageServiceFactory->method('createFromUserPreferences')->willReturn($languageService);

        $toolbarItem = $this->createMock(SystemInformationToolbarItem::class);
        $toolbarItem->expects($this->once())
            ->method('addSystemInformation')
            ->with('Project Version', '9000-rc.69', 'information-git');

        (new SystemInformationListener($service, $languageServiceFactory))(
            new SystemInformationToolbarCollectorEvent($toolbarItem)
        );
    }

    #[Test]
    public function translatesUnknownVersionLabel(): void
    {
        $service = self::createStub(ProjectVersionService::class);
        $service->method('getProjectVersion')->willReturn(new ProjectVersion());

        $languageService = self::createStub(LanguageService::class);
        $languageService->method('sL')->willReturnMap([
            [ProjectVersion::DEFAULT_TITLE, 'Project Version'],
            [ProjectVersion::UNKNOWN_VERSION, 'Unknown project version'],
        ]);
        $languageServiceFactory = self::createStub(LanguageServiceFactory::class);
        $languageServiceFactory->method('createFromUserPreferences')->willReturn($languageService);

        $toolbarItem = $this->createMock(SystemInformationToolbarItem::class);
        $toolbarItem->expects($this->once())
            ->method('addSystemInformation')
            ->with('Project Version', 'Unknown project version', 'information-project-version');

        (new SystemInformationListener($service, $languageServiceFactory))(
            new SystemInformationToolbarCollectorEvent($toolbarItem)
        );
    }
}
