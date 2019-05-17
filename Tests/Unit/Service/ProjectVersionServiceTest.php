<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

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

use KamiYang\ProjectVersion\Configuration\ExtensionConfiguration;
use KamiYang\ProjectVersion\Enumeration\GitCommandEnumeration;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use KamiYang\ProjectVersion\Facade\CommandUtilityFacade;
use KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ProjectVersionServiceTest
 */
class ProjectVersionServiceTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersionService
     */
    protected $subject;

    protected $extensionConfiguration = [
        'gitFormat' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
        'mode' => ProjectVersionModeEnumeration::FILE,
        'staticVersion' => '',
        'versionFilePath' => 'VERSION'
    ];

    /**
     * @var \KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade|\Prophecy\Prophecy\ObjectProphecy
     */
    protected $systemEnvironmentBuilderFacadeProphecy;

    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    protected $commandUtilityFacadeProphecy;

    /**
     * @test
     */
    public function getProjectVersionShouldNotSetProjectVersionIfVersionFileIsNotFound(): void
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
    public function getProjectVersionShouldSetVersionFromVersionFileIfFileExists(string $versionFilePath): void
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

    /**
     * @test
     */
    public function getProjectVersionShouldNotSetVersionFromGitIfCommandExecIsNotAvailable(): void
    {
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::GIT;
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->systemEnvironmentBuilderFacadeProphecy->isFunctionDisabled('exec')
            ->willReturn(true);

        $this->subject->getProjectVersion();

        $projectVersionProphecy->setVersion(Argument::any())
            ->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     *
     * @param string $format
     * @param string $branch
     * @param string $revision
     * @param string $tag
     * @param string $expected
     *
     * @dataProvider gitFormatDataProvider
     */
    public function getProjectVersionShouldReturnSpecifiedVersionBasedOnConfiguredGitFormat(
        string $format,
        string $branch,
        string $revision,
        string $tag,
        string $expected
    ): void {
        // Arrange
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::GIT;
        $this->extensionConfiguration['gitFormat'] = $format;
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->willReturn($branch);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->willReturn($revision);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->willReturn($tag);

        /** @var \KamiYang\ProjectVersion\Service\ProjectVersionService|\PHPUnit\Framework\MockObject\MockObject $subject */
        $subject = $this->createPartialMock(ProjectVersionService::class, ['isGitAvailable']);
        $subject->method('isGitAvailable')->willReturn(true);

        $this->inject($subject, 'commandUtility', $this->commandUtilityFacadeProphecy->reveal());
        $this->inject($subject, 'systemEnvironmentBuilder', $this->systemEnvironmentBuilderFacadeProphecy->reveal());

        // Act
        $actual = $subject->getProjectVersion();

        // Assert
        $projectVersionProphecy->setVersion($expected)->shouldHaveBeenCalled();

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->shouldHaveBeenCalledTimes(1);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->shouldHaveBeenCalledTimes(1);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @test
     */
    public function getProjectVersionShouldTryToFetchVersionFromFileIfResolvingUsingGitErrored(): void
    {
        //Arrange
        $this->extensionConfiguration['versionFilePath'] = 'EXT:project_version/Tests/Fixture/VERSION';
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::GIT_FILE_FALLBACK;
        $this->extensionConfiguration['gitFormat'] = GitCommandEnumeration::FORMAT_REVISION_BRANCH;
        $this->setUpExtensionConfiguration();

        $branch = $revision = $tag = '';

        $absoluteVersionFilename = GeneralUtility::getFileAbsFileName($this->extensionConfiguration['versionFilePath']);
        $expected = file_get_contents($absoluteVersionFilename);

        $projectVersion = new ProjectVersion();
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersion);

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->willReturn($branch);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->willReturn($revision);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->willReturn($tag);

        /** @var \KamiYang\ProjectVersion\Service\ProjectVersionService $subject */
        $subject = $this->createPartialMock(ProjectVersionService::class, ['isGitAvailable']);
        $subject->method('isGitAvailable')->willReturn(true);

        $this->inject($subject, 'commandUtility', $this->commandUtilityFacadeProphecy->reveal());
        $this->inject($subject, 'systemEnvironmentBuilder', $this->systemEnvironmentBuilderFacadeProphecy->reveal());

        // Act
        $subject->getProjectVersion();

        // Assert
        static::assertSame($expected, $projectVersion->getVersion());
    }

    /**
     * @return array
     */
    public function gitFormatDataProvider(): array
    {
        $branch = 'master';
        $revision = 'abcdefg';
        $tag = '9.0.42-rc.2';

        return [
            'default git format' => [
                'format' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => "[{$revision}] {$branch}"
            ],
            'git format: revision' => [
                'format' => GitCommandEnumeration::FORMAT_REVISION,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => (string)$revision
            ],
            'git format: [revision] branch' => [
                'format' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => "[{$revision}] {$branch}"
            ],
            'git format: [revision] tag' => [
                'format' => GitCommandEnumeration::FORMAT_REVISION_TAG,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => "[{$revision}] {$tag}"
            ],
            'git format: branch' => [
                'format' => GitCommandEnumeration::FORMAT_BRANCH,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => $branch
            ],
            'git format: tag' => [
                'format' => GitCommandEnumeration::FORMAT_TAG,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => $tag
            ],
        ];
    }

    /**
     * @test
     */
    public function getProjectVersionShouldAlwaysSetStaticVersionIfSelected(): void
    {
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::STATIC_VERSION;
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->subject->getProjectVersion();

        $projectVersionProphecy->setVersion('')->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @test
     * @param string $staticVersion
     * @dataProvider staticVersionDataProvider
     */
    public function getProjectVersionShouldSetStaticVersionFromExtensionConfigurationIfSelected(string $staticVersion): void
    {
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::STATIC_VERSION;
        $this->extensionConfiguration['staticVersion'] = $staticVersion;
        $this->setUpExtensionConfiguration();

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $this->subject->getProjectVersion();

        $projectVersionProphecy->setVersion($staticVersion)->shouldHaveBeenCalledTimes(1);
    }

    public function staticVersionDataProvider(): array
    {
        return [
            'empty static version (default value)' => [
                'staticVersion' => ''
            ],
            'some value' => [
                'staticVersion' => 'some value'
            ],
            'some extreme long value' => [
                'staticVersion' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos hic ipsa labore molestiae nesciunt quo repellendus similique tenetur vitae voluptatem! Dicta dolor minus nostrum ratione voluptas? Ad animi iste sunt!'
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->systemEnvironmentBuilderFacadeProphecy = $this->prophesize(SystemEnvironmentBuilderFacade::class);
        $this->systemEnvironmentBuilderFacadeProphecy->isFunctionDisabled('exec')
            ->willReturn(false);

        $this->commandUtilityFacadeProphecy = $this->prophesize(CommandUtilityFacade::class);

        $this->subject = new ProjectVersionService(
            $this->commandUtilityFacadeProphecy->reveal(),
            $this->systemEnvironmentBuilderFacadeProphecy->reveal()
        );
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    protected function setUpExtensionConfiguration(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['project_version'] = serialize($this->extensionConfiguration);

        GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }
}
