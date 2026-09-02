<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Configuration;

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
use KamiYang\ProjectVersion\Enumeration\GitFormat;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as CoreExtensionConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ExtensionConfigurationTest extends UnitTestCase
{
    /**
     * @param array<string, mixed> $settings
     */
    private function createSubject(array $settings): ExtensionConfiguration
    {
        $core = self::createStub(CoreExtensionConfiguration::class);
        $core->method('get')->willReturn($settings);

        return new ExtensionConfiguration($core);
    }

    #[Test]
    public function fallsBackToDefaultsIfExtensionIsNotConfigured(): void
    {
        $core = self::createStub(CoreExtensionConfiguration::class);
        $core->method('get')->willThrowException(new ExtensionConfigurationExtensionNotConfiguredException('', 1));

        $extensionConfiguration = new ExtensionConfiguration($core);

        self::assertSame('VERSION', $extensionConfiguration->getVersionFilePath());
        self::assertSame(ProjectVersionMode::File, $extensionConfiguration->getMode());
        self::assertSame(GitFormat::RevisionBranch, $extensionConfiguration->getGitFormat());
        self::assertSame('', $extensionConfiguration->getStaticVersion());
    }

    /**
     * @return \Generator<string, array{string, string}>
     */
    public static function versionFilePathDataProvider(): \Generator
    {
        yield 'empty path defaults to VERSION' => ['', 'VERSION'];
        yield 'whitespace only defaults to VERSION' => ['  ', 'VERSION'];
        yield 'directory gets VERSION appended' => ['typo3conf/', 'typo3conf/VERSION'];
        yield 'file name is kept' => ['MyVersion', 'MyVersion'];
        yield 'EXT: notation is kept' => ['EXT:project_version/Tests/Fixture/VERSION', 'EXT:project_version/Tests/Fixture/VERSION'];
        yield 'absolute path is kept' => ['/srv/app/VERSION', '/srv/app/VERSION'];
    }

    #[Test]
    #[DataProvider('versionFilePathDataProvider')]
    public function getVersionFilePathCompletesDirectories(string $configured, string $expected): void
    {
        self::assertSame($expected, $this->createSubject(['versionFilePath' => $configured])->getVersionFilePath());
    }

    #[Test]
    public function getAbsVersionFilePathReturnsAbsolutePathIfFileExists(): void
    {
        $fixture = realpath(__DIR__ . '/../../Fixture/VERSION');

        self::assertSame($fixture, $this->createSubject(['versionFilePath' => $fixture])->getAbsVersionFilePath());
    }

    #[Test]
    public function getAbsVersionFilePathReturnsEmptyStringIfFileDoesNotExist(): void
    {
        self::assertSame('', $this->createSubject(['versionFilePath' => '/does/not/exist/VERSION'])->getAbsVersionFilePath());
        self::assertSame('', $this->createSubject(['versionFilePath' => 'does/not/exist/VERSION'])->getAbsVersionFilePath());
    }

    #[Test]
    public function getModeMapsConfiguredValueToEnum(): void
    {
        self::assertSame(ProjectVersionMode::Git, $this->createSubject(['mode' => '1'])->getMode());
        self::assertSame(ProjectVersionMode::GitFileFallback, $this->createSubject(['mode' => 2])->getMode());
        self::assertSame(ProjectVersionMode::File, $this->createSubject(['mode' => 'garbage'])->getMode());
    }

    #[Test]
    public function getGitFormatMapsConfiguredValueToEnum(): void
    {
        self::assertSame(GitFormat::Tag, $this->createSubject(['gitFormat' => '4'])->getGitFormat());
        self::assertSame(GitFormat::RevisionBranch, $this->createSubject(['gitFormat' => '99'])->getGitFormat());
    }

    #[Test]
    public function getStaticVersionIsTrimmed(): void
    {
        self::assertSame('1.2.3', $this->createSubject(['staticVersion' => ' 1.2.3 '])->getStaticVersion());
    }
}
