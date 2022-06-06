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

namespace KamiYang\ProjectVersion\Configuration;

use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Class ExtensionConfiguration
 */
final class ExtensionConfiguration implements SingletonInterface
{
    private const DEFAULT_VERSION_FILE = 'VERSION';

    /**
     * Extension configuration.
     *
     * @var array
     */
    private $configuration;

    /**
     * Relative file path of the VERSION-file. Blank equals const 'PATH_site'.
     *
     * @var string
     */
    private $versionFilePath;

    /**
     * Indicator for the fetching method.
     *
     * @var string
     * @see \KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration
     */
    private $mode;

    /**
     * @var string
     */
    private $gitFormat;

    /**
     * @var string
     */
    private $staticVersion;

    /**
     * Fetch absolute version filename.
     *
     * @return string
     */
    public function getAbsVersionFilePath(): string
    {
        return GeneralUtility::getFileAbsFileName($this->getVersionFilePath());
    }

    public function getVersionFilePath(): string
    {
        return $this->versionFilePath;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getGitFormat(): string
    {
        return $this->gitFormat;
    }

    public function getStaticVersion(): string
    {
        return $this->staticVersion;
    }

    public function __construct()
    {
        $this->configuration = $this->getExtensionConfigurationFromGlobals();

        $this->versionFilePath = $this->resolveVersionFilePath();
        $this->mode = $this->configuration['mode'] ?? ProjectVersionModeEnumeration::FILE;
        $this->gitFormat = $this->configuration['gitFormat'] ?? '';
        $this->staticVersion = $this->configuration['staticVersion'] ?? '';
    }

    private function getExtensionConfigurationFromGlobals(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['project_version'] ?? [];
    }

    private function resolveVersionFilePath(): string
    {
        $pathFromConfiguration = $this->configuration['versionFilePath'] ?? '';

        if (empty($pathFromConfiguration) || $this->isDirectory($pathFromConfiguration)) {
            $pathFromConfiguration .= self::DEFAULT_VERSION_FILE;
        }

        return $pathFromConfiguration;
    }

    private function isDirectory(string $pathFromConfiguration): bool
    {
        return StringUtility::endsWith($pathFromConfiguration, '/');
    }
}
