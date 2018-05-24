<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Backend\ToolbarItems;

use KamiYang\ProjectVersion\Backend\ToolbarItems\ProjectVersionSlot;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionSlotTest
 *
 * @package KamiYang\ProjectVersion\Tests\Unit\Backend\ToolbarItems
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
    public function asd()
    {
        $title = 'Project Version';
        $version = '9000-rc.69';

        $systemInformationToolbarItemProphecy = $this->prophesize(SystemInformationToolbarItem::class);

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        $projectVersionProphecy->getTitle()
            ->willReturn($title);
        $projectVersionProphecy->getVersion()
            ->willReturn($version);

        $projectVersionServiceProphecy = $this->prophesize(ProjectVersionService::class);
        $projectVersionServiceProphecy->getProjectVersion()
            ->willReturn($projectVersionProphecy->reveal());

        GeneralUtility::setSingletonInstance(ProjectVersionService::class, $projectVersionServiceProphecy->reveal());

        $this->subject->getProjectVersion($systemInformationToolbarItemProphecy->reveal());

        $projectVersionProphecy->getTitle()
            ->shouldHaveBeenCalledTimes(1);
        $projectVersionProphecy->getVersion()
            ->shouldHaveBeenCalledTimes(1);
        $systemInformationToolbarItemProphecy->addSystemInformation($title, $version, '')
            ->shouldHaveBeenCalledTimes(1);
    }
}
