<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Backend\ToolbarItems;

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

use KamiYang\ProjectVersion\Backend\ToolbarItems\ProjectVersionSlot;
use KamiYang\ProjectVersion\Facade\LocalizationUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProjectVersionSlotTest
 *
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class ProjectVersionSlotTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Backend\ToolbarItems\ProjectVersionSlot
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new ProjectVersionSlot();
    }

    protected function tearDown()
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getProjectVersionShouldAddProjectVersionAsSystemInformation()
    {
        $version = '9000-rc.69';
        $title = 'Project Version';
        $iconIdentifier = 'information-project-version';

        $projectVersion = new ProjectVersion();
        $projectVersion->setVersion($version);
        $projectVersion->setTitle($title);

        $systemInformationToolbarItemProphecy = $this->prophesize(SystemInformationToolbarItem::class);

        $projectVersionServiceProphecy = $this->prophesize(ProjectVersionService::class);
        $projectVersionServiceProphecy->getProjectVersion()->willReturn($projectVersion);

        $objectManagerProphecy = $this->prophesize(ObjectManager::class);
        $objectManagerProphecy->get(ProjectVersionService::class)->willReturn($projectVersionServiceProphecy->reveal());

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManagerProphecy->reveal());
        GeneralUtility::setSingletonInstance(ProjectVersionService::class, $projectVersionServiceProphecy->reveal());

        $this->subject->getProjectVersion($systemInformationToolbarItemProphecy->reveal());

        $systemInformationToolbarItemProphecy->addSystemInformation($title, $version, $iconIdentifier)
            ->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @test
     */
    public function getProjectVersionShouldResolveCurrentVersionAndLocalizeItIfNecessary()
    {
        $initialVersionValue = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown';
        $projectVersion = new ProjectVersion();
        $projectVersion->setVersion($initialVersionValue);
        $expectedVersion = 'Unknown project version';

        $projectVersionServiceProphecy = $this->prophesize(ProjectVersionService::class);
        $projectVersionServiceProphecy->getProjectVersion()->willReturn($projectVersion);

        $objectManagerProphecy = $this->prophesize(ObjectManager::class);
        $objectManagerProphecy->get(ProjectVersionService::class)->willReturn($projectVersionServiceProphecy->reveal());

        $localizationUtilityFacadeProphecy = $this->prophesize(LocalizationUtilityFacade::class);
        $localizationUtilityFacadeProphecy->translate($initialVersionValue)->willReturn($expectedVersion);

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManagerProphecy->reveal());
        GeneralUtility::setSingletonInstance(ProjectVersionService::class, $projectVersionServiceProphecy->reveal());
        GeneralUtility::addInstance(LocalizationUtilityFacade::class, $localizationUtilityFacadeProphecy->reveal());

        $systemInformationToolbarItemProphecy = $this->prophesize(SystemInformationToolbarItem::class);
        $actual = $this->subject->getProjectVersion($systemInformationToolbarItemProphecy->reveal());

        $systemInformationToolbarItemProphecy->addSystemInformation(
            $projectVersion->getTitle(),
            $expectedVersion,
            $projectVersion->getIconIdentifier()
        )
            ->shouldHaveBeenCalledTimes(1);
    }
}
