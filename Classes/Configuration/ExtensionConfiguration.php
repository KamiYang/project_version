<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Configuration;

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

use KamiYang\ProjectVersion\Enumeration\GitFormat;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionMode;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as CoreExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Typed access to the extension settings of EXT:project_version.
 */
class ExtensionConfiguration
{
    public const EXTENSION_KEY = 'project_version';

    public const DEFAULT_VERSION_FILE_NAME = 'VERSION';

    /**
     * @var array<string, mixed>
     */
    private array $configuration;

    public function __construct(CoreExtensionConfiguration $coreExtensionConfiguration)
    {
        try {
            $configuration = $coreExtensionConfiguration->get(self::EXTENSION_KEY);
        } catch (\Throwable) {
            // Extension settings have not been written yet: fall back to the defaults below.
            $configuration = [];
        }

        $this->configuration = is_array($configuration) ? $configuration : [];
    }

    /**
     * Configured path of the version file. A directory (trailing slash) is completed with 'VERSION'.
     */
    public function getVersionFilePath(): string
    {
        $path = trim((string)($this->configuration['versionFilePath'] ?? ''));

        if ($path === '' || str_ends_with($path, '/')) {
            $path .= self::DEFAULT_VERSION_FILE_NAME;
        }

        return $path;
    }

    /**
     * Absolute path of the version file.
     *
     * Resolution order for relative paths: 'EXT:' notation, the public web directory, then the
     * project directory. Absolute paths are used as given. Returns an empty string if the file
     * does not exist anywhere.
     */
    public function getAbsVersionFilePath(): string
    {
        $path = $this->getVersionFilePath();

        if (str_starts_with($path, 'EXT:')) {
            return GeneralUtility::getFileAbsFileName($path);
        }

        if (str_starts_with($path, '/')) {
            return is_file($path) ? $path : '';
        }

        foreach ([Environment::getPublicPath(), Environment::getProjectPath()] as $basePath) {
            $candidate = rtrim($basePath, '/') . '/' . $path;
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    public function getMode(): ProjectVersionMode
    {
        return ProjectVersionMode::tryFrom((string)($this->configuration['mode'] ?? '')) ?? ProjectVersionMode::File;
    }

    public function getGitFormat(): GitFormat
    {
        return GitFormat::tryFrom((string)($this->configuration['gitFormat'] ?? '')) ?? GitFormat::RevisionBranch;
    }

    public function getStaticVersion(): string
    {
        return trim((string)($this->configuration['staticVersion'] ?? ''));
    }
}
