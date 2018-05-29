<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

use KamiYang\ProjectVersion\Configuration\ExtensionConfiguration;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionServiceTest
 *
 * @package KamiYang\ProjectVersion\Tests\Unit\Service
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class ProjectVersionServiceTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersionService
     */
    private $subject;

    private $extensionConfiguration = [
        'versionFilePath' => 'VERSION'
    ];

    protected function setUp()
    {
        $this->subject = new ProjectVersionService();

    }

    protected function tearDown()
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getProjectVersionShouldNotSetProjectVersionIfVersionFileIsNotFound()
    {
        $this->extensionConfiguration['versionFilePath'] = '/some/not/existing/path';
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->subject->getProjectVersion();

        $projectVersionProphecy->setVersion(Argument::any())
            ->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     * @param string $versionFilePath
     * @dataProvider versionFilePathDataProvider
     */
    public function getProjectVersionShouldSetVersionFromVersionFileIfFileExists(string $versionFilePath)
    {
        $this->extensionConfiguration['versionFilePath'] = $versionFilePath;
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->subject->getProjectVersion();

        $projectVersionProphecy->setVersion(Argument::containingString('1.0.1'))
            ->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @return array
     */
    public function versionFilePathDataProvider(): array
    {
        return [
            'version file with EXT shortcut' => [
                'EXT:project_version/Tests/Fixture/VERSION'
            ],
            'directory with EXT shortcut' => [
                'EXT:project_version/Tests/Fixture/'
            ],
            'Version file with EXT shortcut and different filename' => [
                'EXT:project_version/Tests/Fixture/VersionFileWithDifferentName'
            ]
        ];
    }

    protected function setUpExtensionConfiguration()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['project_version'] = serialize($this->extensionConfiguration);

        GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }
}
