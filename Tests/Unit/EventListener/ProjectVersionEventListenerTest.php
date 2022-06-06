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

namespace KamiYang\ProjectVersion\Tests\Unit\EventListener;

use KamiYang\ProjectVersion\EventListener\ProjectVersionEventListener;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProjectVersionSlotTest
 */
class ProjectVersionEventListenerTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Localization\LanguageService|\Prophecy\Prophecy\ObjectProphecy
     */
    private $languageServiceProphecy;

    protected function setUp(): void
    {
        $this->languageServiceProphecy = $this->prophesize(LanguageService::class);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getProjectVersionShouldAddProjectVersionAsSystemInformation(): void
    {
        $version = '9000-rc.69';
        $title = 'Project Version';
        $iconIdentifier = 'information-project-version';

        $projectVersion = new ProjectVersion();
        $projectVersion->setVersion($version);
        $projectVersion->setTitle($title);

        $systemInformationToolbarItemProphecy = $this->prophesize(SystemInformationToolbarItem::class);
        $event = new SystemInformationToolbarCollectorEvent($systemInformationToolbarItemProphecy->reveal());
        $subject = $this->getSubject($projectVersion);
        $subject($event);

        $systemInformationToolbarItemProphecy->addSystemInformation($title, $version, $iconIdentifier)
            ->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @test
     */
    public function getProjectVersionShouldResolveCurrentVersionAndLocalizeItIfNecessary(): void
    {
        $initialVersionValue = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown';
        $projectVersion = new ProjectVersion();
        $projectVersion->setVersion($initialVersionValue);
        $expectedVersion = 'Unknown project version';

        $this->languageServiceProphecy->sL($initialVersionValue)->willReturn($expectedVersion);

        $systemInformationToolbarItemProphecy = $this->prophesize(SystemInformationToolbarItem::class);
        $event = new SystemInformationToolbarCollectorEvent($systemInformationToolbarItemProphecy->reveal());
        $subject = $this->getSubject($projectVersion);
        $subject($event);

        $systemInformationToolbarItemProphecy->addSystemInformation(
            $projectVersion->getTitle(),
            $expectedVersion,
            $projectVersion->getIconIdentifier()
        )
            ->shouldHaveBeenCalledTimes(1);
    }

    private function getSubject(ProjectVersion $projectVersion): ProjectVersionEventListener
    {
        $projectVersionServiceProphecy = $this->prophesize(ProjectVersionService::class);
        $projectVersionServiceProphecy->getProjectVersion()->willReturn($projectVersion);

        return new ProjectVersionEventListener(
            $projectVersionServiceProphecy->reveal(),
            $this->languageServiceProphecy->reveal()
        );
    }
}
