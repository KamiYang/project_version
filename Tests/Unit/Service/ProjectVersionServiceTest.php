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

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

use Generator;
use KamiYang\ProjectVersion\Configuration\ExtensionConfiguration;
use KamiYang\ProjectVersion\Enumeration\GitCommandEnumeration;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use KamiYang\ProjectVersion\Facade\CommandUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_replace;
use function ini_get;
use function ini_set;

class ProjectVersionServiceTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersionService
     */
    private $subject;

    private $defaultExtensionConfiguration = [
        'gitFormat' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
        'mode' => ProjectVersionModeEnumeration::FILE,
        'staticVersion' => '',
        'versionFilePath' => 'VERSION',
    ];

    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    private $commandUtilityFacadeProphecy;

    protected function setUp(): void
    {
        $this->commandUtilityFacadeProphecy = $this->prophesize(CommandUtilityFacade::class);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();
    }

    public function testGetProjectVersionShouldNotSetProjectVersionIfVersionFileIsNotFound(): void
    {
        $this->setUpExtensionConfiguration([
            'versionFilePath' => '/some/not/existing/path',
        ]);

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());
        $_ = $this->getSubject();

        $projectVersionProphecy->setVersion(Argument::any())
            ->shouldNotHaveBeenCalled();
    }

    /**
     * @dataProvider versionFilePathDataProvider
     */
    public function testGetProjectVersionShouldSetVersionFromVersionFileIfFileExists(string $versionFilePath): void
    {
        $this->setUpExtensionConfiguration([
            'versionFilePath' => $versionFilePath,
        ]);

        $subject = $this->getSubject();

        self::assertEquals(
            '1.0.1',
            $subject->getProjectVersion()->getVersion()
        );
    }

    public function versionFilePathDataProvider(): Generator
    {
        yield 'version file with EXT shortcut' => [
            'EXT:project_version/Tests/Fixture/VERSION',
        ];
        yield 'directory with EXT shortcut' => [
            'EXT:project_version/Tests/Fixture/',
        ];
        yield 'Version file with EXT shortcut and different filename' => [
            'EXT:project_version/Tests/Fixture/VersionFileWithDifferentName',
        ];
    }

    public function testGetProjectVersionShouldNotSetVersionFromGitIfCommandExecIsNotAvailable(): void
    {
        $this->setUpExtensionConfiguration([
            'mode' => ProjectVersionModeEnumeration::GIT,
        ]);

        $projectVersionProphecy = $this->prophesize(ProjectVersion::class);
        GeneralUtility::setSingletonInstance(ProjectVersion::class, $projectVersionProphecy->reveal());

        $originalDisbableFunctions = ini_get('disable_functions');
        ini_set('disable_functions', 'exec');

        $_ = $this->getSubject();

        $projectVersionProphecy->setVersion(Argument::any())
            ->shouldNotHaveBeenCalled();

        ini_set('disable_functions', $originalDisbableFunctions);
    }

    /**
     * @dataProvider gitFormatDataProvider
     */
    public function testGetProjectVersionShouldReturnSpecifiedVersionBasedOnConfiguredGitFormat(
        string $format,
        string $branch,
        string $revision,
        string $tag,
        string $expected
    ): void {
        $this->setUpExtensionConfiguration([
            'mode' => ProjectVersionModeEnumeration::GIT,
            'gitFormat' => $format,
        ]);
        $returnValue = 0;
        $this->commandUtilityFacadeProphecy->exec('git --version', $_, $returnValue)
            ->will(function (&$arguments) {
                $arguments[2] = 0;
            });
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->willReturn($branch);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->willReturn($revision);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->willReturn($tag);

        $subject = $this->createPartialMock(ProjectVersionService::class, ['isGitAvailable']);
        $this->inject($subject, 'extensionConfiguration', new ExtensionConfiguration());
        $this->inject($subject, 'commandUtilityFacade', $this->commandUtilityFacadeProphecy->reveal());
        $subject
            ->expects(self::once())
            ->method('isGitAvailable')
            ->willReturn(true);

        static::assertSame(
            $expected,
            $subject->getProjectVersion()->getVersion()
        );

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->shouldHaveBeenCalledTimes(1);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->shouldHaveBeenCalledTimes(1);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->shouldHaveBeenCalledTimes(1);
    }

    public function testGetProjectVersionShouldTryToFetchVersionFromFileIfResolvingUsingGitErrored(): void
    {
        $versionFilePath = 'EXT:project_version/Tests/Fixture/VERSION';
        $this->setUpExtensionConfiguration([
            'versionFilePath' => $versionFilePath,
            'mode' => ProjectVersionModeEnumeration::GIT_FILE_FALLBACK,
            'gitFormat' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
        ]);
        $branch = '';
        $revision = '';
        $tag = '';
        $absoluteVersionFilename = GeneralUtility::getFileAbsFileName($versionFilePath);
        $expected = trim(file_get_contents($absoluteVersionFilename));

        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_BRANCH)->willReturn($branch);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_REVISION)->willReturn($revision);
        $this->commandUtilityFacadeProphecy->exec(GitCommandEnumeration::CMD_TAG)->willReturn($tag);
        $this->commandUtilityFacadeProphecy->exec('git --version', $_, $returnCode)->willReturn(0);

        $subject = $this->getSubject();

        static::assertSame($expected, $subject->getProjectVersion()->getVersion());
    }

    public function gitFormatDataProvider(): Generator
    {
        $branch = 'master';
        $revision = 'abcdefg';
        $tag = '9.0.42-rc.2';

        yield 'default git format' => [
            'format' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "[{$revision}] {$branch}",
        ];
        yield 'git format: revision' => [
            'format' => GitCommandEnumeration::FORMAT_REVISION,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "{$revision}",
        ];
        yield 'git format: [revision] branch' => [
            'format' => GitCommandEnumeration::FORMAT_REVISION_BRANCH,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "[{$revision}] {$branch}",
        ];
        yield 'git format: [revision] tag' => [
            'format' => GitCommandEnumeration::FORMAT_REVISION_TAG,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "[{$revision}] {$tag}",
        ];
        yield 'git format: branch' => [
            'format' => GitCommandEnumeration::FORMAT_BRANCH,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "{$branch}",
        ];
        yield 'git format: tag' => [
            'format' => GitCommandEnumeration::FORMAT_TAG,
            'branch' => $branch,
            'revision' => $revision,
            'tag' => $tag,
            'expected' => "{$tag}",
        ];
    }

    public function testGetProjectVersionShouldAlwaysSetStaticVersionIfSelected(): void
    {
        $this->setUpExtensionConfiguration([
            'mode' => ProjectVersionModeEnumeration::STATIC_VERSION,
        ]);

        $subject = $this->getSubject();

        static::assertSame(
            '',
            $subject->getProjectVersion()->getVersion()
        );
    }

    /**
     * @dataProvider staticVersionDataProvider
     */
    public function testGetProjectVersionShouldSetStaticVersionFromExtensionConfigurationIfSelected(
        string $staticVersion
    ): void {
        $this->setUpExtensionConfiguration([
            'mode' => ProjectVersionModeEnumeration::STATIC_VERSION,
            'staticVersion' => $staticVersion,
        ]);

        $subject = $this->getSubject();

        self::assertSame(
            $staticVersion,
            $subject->getProjectVersion()->getVersion()
        );
    }

    public function staticVersionDataProvider(): Generator
    {
        yield 'empty static version (default value)' => [
            'staticVersion' => '',
        ];
        yield 'some value' => [
            'staticVersion' => 'some value',
        ];
        yield 'some extreme long value' => [
            'staticVersion' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos hic ipsa labore molestiae nesciunt quo repellendus similique tenetur vitae voluptatem! Dicta dolor minus nostrum ratione voluptas? Ad animi iste sunt!',
        ];
    }

    protected function setUpExtensionConfiguration(array $extConfig): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['project_version'] = array_replace(
            $this->defaultExtensionConfiguration,
            $extConfig
        );
    }

    private function getSubject(): ProjectVersionService
    {
        return new ProjectVersionService(
            $this->commandUtilityFacadeProphecy->reveal(),
            new ExtensionConfiguration()
        );
    }
}
