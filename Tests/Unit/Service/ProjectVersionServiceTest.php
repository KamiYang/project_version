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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionServiceTest
 *
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class ProjectVersionServiceTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersionService
     */
    private $subject;

    private $extensionConfiguration = [
        'versionFilePath' => 'VERSION',
        'mode' => ProjectVersionModeEnumeration::FILE
    ];

    /**
     * @var \KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade|\Prophecy\Prophecy\ObjectProphecy
     */
    private $systemEnvironmentBuilderFacadeProphecy;

    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    private $commandUtilityFacadeProphecy;

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

    /**
     * @test
     */
    public function getProjectVersionShouldNotSetVersionFromGitIfCommandExecIsNotAvailable()
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
    ) {
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
        $subject->injectCommandUtilityFacade($this->commandUtilityFacadeProphecy->reveal());
        $subject->injectSystemEnvironmentBuilderFacade($this->systemEnvironmentBuilderFacadeProphecy->reveal());

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
    public function getProjectVersionShouldTryToFetchVersionFromFileIfResolvingUsingGitErrored()
    {
        //Arrange
        $this->extensionConfiguration['versionFilePath'] = 'EXT:project_version/Tests/Fixture/VERSION';
        $this->extensionConfiguration['mode'] = ProjectVersionModeEnumeration::GIT_FILE_FALLBACK;
        $this->extensionConfiguration['gitFormat'] = GitCommandEnumeration::FORMAT_REVISION_BRANCH;
        $this->setUpExtensionConfiguration();
        $branch = '';
        $revision = '';
        $tag = '';
        $absoluteVersionFilename = GeneralUtility::getFileAbsFileName($this->extensionConfiguration['versionFilePath']);
        $expected = \file_get_contents($absoluteVersionFilename);

        $projectVersion = new ProjectVersion();
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersion);

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->willReturn($branch);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->willReturn($revision);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->willReturn($tag);

        /** @var \KamiYang\ProjectVersion\Service\ProjectVersionService $subject */
        $subject = $this->createPartialMock(ProjectVersionService::class, ['isGitAvailable']);
        $subject->method('isGitAvailable')->willReturn(true);
        $subject->injectCommandUtilityFacade($this->commandUtilityFacadeProphecy->reveal());
        $subject->injectSystemEnvironmentBuilderFacade($this->systemEnvironmentBuilderFacadeProphecy->reveal());

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
                'expected' => "{$revision}"
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
                'expected' => "{$branch}"
            ],
            'git format: tag' => [
                'format' => GitCommandEnumeration::FORMAT_TAG,
                'branch' => $branch,
                'revision' => $revision,
                'tag' => $tag,
                'expected' => "{$tag}"
            ],
        ];
    }

    protected function setUp()
    {
        $this->systemEnvironmentBuilderFacadeProphecy = $this->prophesize(SystemEnvironmentBuilderFacade::class);
        $this->systemEnvironmentBuilderFacadeProphecy->isFunctionDisabled('exec')
            ->willReturn(false);

        $this->commandUtilityFacadeProphecy = $this->prophesize(CommandUtilityFacade::class);

        $this->subject = new ProjectVersionService();
        $this->subject->injectCommandUtilityFacade($this->commandUtilityFacadeProphecy->reveal());
        $this->subject->injectSystemEnvironmentBuilderFacade($this->systemEnvironmentBuilderFacadeProphecy->reveal());
    }

    protected function tearDown()
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    protected function setUpExtensionConfiguration()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['project_version'] = serialize($this->extensionConfiguration);

        GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }
}
