<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

use KamiYang\ProjectVersion\Facade\LocalizationUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Class ProjectVersionTest.
 *
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class ProjectVersionTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersion
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new ProjectVersion();
    }

    /**
     * @test
     */
    public function getTitleShouldReturnInitialValue()
    {
        static::assertSame(
            'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleShouldSetPropertyTitle()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setTitle($newValue);

        static::assertAttributeSame($newValue, 'title', $this->subject);
    }

    /**
     * @test
     */
    public function initialVersionValueShouldBeLLLString()
    {
        static::assertAttributeSame(
            'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown',
            'version',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getVersionShouldReturnCurrentVersionAndLocalizeItIfNecessary()
    {
        $initialVersionValue = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown';
        $expected = 'Unknown project version ';

        $localizationUtilityFacadeProphecy = $this->prophesize(LocalizationUtilityFacade::class);
        $localizationUtilityFacadeProphecy->translate($initialVersionValue)
            ->shouldBeCalledTimes(1)
            ->willReturn($expected);

        GeneralUtility::addInstance(
            LocalizationUtilityFacade::class,
            $localizationUtilityFacadeProphecy->reveal()
        );

        static::assertSame($expected, $this->subject->getVersion());
    }

    /**
     * @test
     */
    public function setVersionShouldSetPropertyVersion()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setVersion($newValue);

        static::assertAttributeSame($newValue, 'version', $this->subject);
    }

    /**
     * @test
     */
    public function getIconIdentifierShouldReturnInitialValue()
    {
        static::assertSame('information-project-version', $this->subject->getIconIdentifier());
    }

    /**
     * @test
     */
    public function setIconIdentifierShouldSetPropertyIconIdentifier()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setIconIdentifier($newValue);

        static::assertAttributeSame($newValue, 'iconIdentifier', $this->subject);
    }
}
