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
use KamiYang\ProjectVersion\Enumeration\GitCommand;
use KamiYang\ProjectVersion\Enumeration\GitFormat;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionMode;
use KamiYang\ProjectVersion\Facade\CommandUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersion;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as CoreExtensionConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ProjectVersionServiceTest extends UnitTestCase
{
    private const FIXTURE_DIR = __DIR__ . '/../../Fixture/';

    private CommandUtilityFacade&Stub $commandUtilityFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandUtilityFacade = self::createStub(CommandUtilityFacade::class);
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function createSubject(array $settings): ProjectVersionService
    {
        $settings += [
            'versionFilePath' => self::FIXTURE_DIR . 'VERSION',
            'mode' => ProjectVersionMode::File->value,
            'gitFormat' => GitFormat::RevisionBranch->value,
            'staticVersion' => '',
        ];
        $core = self::createStub(CoreExtensionConfiguration::class);
        $core->method('get')->willReturn($settings);

        return new ProjectVersionService(new ExtensionConfiguration($core), $this->commandUtilityFacade);
    }

    /**
     * Let the mocked shell report a working git binary and the given git output.
     */
    private function gitReturns(string $branch, string $revision, string $tag): void
    {
        $this->commandUtilityFacade->method('isExecAvailable')->willReturn(true);
        $this->commandUtilityFacade->method('exec')->willReturnCallback(
            static function (string $command, ?array &$output = null, int &$returnValue = 0) use ($branch, $revision, $tag): string {
                $returnValue = 0;

                return match ($command) {
                    GitCommand::Version->value => 'git version 2.43.0',
                    GitCommand::Branch->value => $branch,
                    GitCommand::Revision->value => $revision,
                    GitCommand::Tag->value => $tag,
                    default => '',
                };
            }
        );
    }

    #[Test]
    public function versionStaysUnknownIfVersionFileIsNotFound(): void
    {
        $actual = $this->createSubject(['versionFilePath' => '/some/not/existing/path'])->getProjectVersion();

        self::assertTrue($actual->isUnknown());
        self::assertSame(ProjectVersion::DEFAULT_ICON_IDENTIFIER, $actual->getIconIdentifier());
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function versionFilePathDataProvider(): \Generator
    {
        yield 'version file' => [self::FIXTURE_DIR . 'VERSION'];
        yield 'directory' => [self::FIXTURE_DIR];
        yield 'version file with different name' => [self::FIXTURE_DIR . 'VersionFileWithDifferentName'];
    }

    #[Test]
    #[DataProvider('versionFilePathDataProvider')]
    public function versionIsReadFromFile(string $versionFilePath): void
    {
        $actual = $this->createSubject(['versionFilePath' => $versionFilePath])->getProjectVersion();

        self::assertSame('1.0.1', $actual->getVersion());
    }

    #[Test]
    public function staticVersionIsUsed(): void
    {
        $actual = $this->createSubject([
            'mode' => ProjectVersionMode::StaticVersion->value,
            'staticVersion' => '4.2.0',
        ])->getProjectVersion();

        self::assertSame('4.2.0', $actual->getVersion());
    }

    #[Test]
    public function emptyStaticVersionStaysUnknown(): void
    {
        $actual = $this->createSubject(['mode' => ProjectVersionMode::StaticVersion->value])->getProjectVersion();

        self::assertTrue($actual->isUnknown());
    }

    #[Test]
    public function gitIsNotUsedIfExecIsDisabled(): void
    {
        $facade = $this->createMock(CommandUtilityFacade::class);
        $facade->method('isExecAvailable')->willReturn(false);
        $facade->expects($this->never())->method('exec');
        $this->commandUtilityFacade = $facade;

        $actual = $this->createSubject(['mode' => ProjectVersionMode::Git->value])->getProjectVersion();

        self::assertTrue($actual->isUnknown());
    }

    #[Test]
    public function gitIsNotUsedIfGitBinaryIsMissing(): void
    {
        $facade = $this->createMock(CommandUtilityFacade::class);
        $facade->method('isExecAvailable')->willReturn(true);
        $facade->expects($this->once())->method('exec')->willReturnCallback(
            static function (string $command, ?array &$output = null, int &$returnValue = 0): string {
                $returnValue = 127;

                return '';
            }
        );
        $this->commandUtilityFacade = $facade;

        $actual = $this->createSubject(['mode' => ProjectVersionMode::Git->value])->getProjectVersion();

        self::assertTrue($actual->isUnknown());
    }

    /**
     * @return \Generator<string, array{GitFormat, string}>
     */
    public static function gitFormatDataProvider(): \Generator
    {
        yield 'revision' => [GitFormat::Revision, 'abc1234'];
        yield '[revision] branch' => [GitFormat::RevisionBranch, '[abc1234] main'];
        yield '[revision] tag' => [GitFormat::RevisionTag, '[abc1234] v1.0.0'];
        yield 'branch' => [GitFormat::Branch, 'main'];
        yield 'tag' => [GitFormat::Tag, 'v1.0.0'];
    }

    #[Test]
    #[DataProvider('gitFormatDataProvider')]
    public function gitVersionIsFormattedAsConfigured(GitFormat $format, string $expected): void
    {
        $this->gitReturns('main', 'abc1234', 'v1.0.0');

        $actual = $this->createSubject([
            'mode' => ProjectVersionMode::Git->value,
            'gitFormat' => $format->value,
        ])->getProjectVersion();

        self::assertSame($expected, $actual->getVersion());
        self::assertSame(ProjectVersion::GIT_ICON_IDENTIFIER, $actual->getIconIdentifier());
    }

    #[Test]
    public function gitFallbackReadsFileIfGitReturnsNothing(): void
    {
        $this->gitReturns('', '', '');

        $actual = $this->createSubject(['mode' => ProjectVersionMode::GitFileFallback->value])->getProjectVersion();

        self::assertSame('1.0.1', $actual->getVersion());
        self::assertSame(ProjectVersion::DEFAULT_ICON_IDENTIFIER, $actual->getIconIdentifier());
    }

    #[Test]
    public function gitFallbackPrefersGit(): void
    {
        $this->gitReturns('main', 'abc1234', '');

        $actual = $this->createSubject(['mode' => ProjectVersionMode::GitFileFallback->value])->getProjectVersion();

        self::assertSame('[abc1234] main', $actual->getVersion());
    }
}
