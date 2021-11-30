<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function file_exists;
use function file_get_contents;
use function in_array;
use function ini_get;
use function sprintf;
use function trim;

/**
 * Class ProjectVersionService
 */
class ProjectVersionService implements SingletonInterface
{
    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    protected $commandUtilityFacade;
    /**
     * @var \KamiYang\ProjectVersion\Configuration\ExtensionConfiguration
     */
    private $extensionConfiguration;

    public function __construct(CommandUtilityFacade $commandUtilityFacade, ExtensionConfiguration $extensionConfiguration)
    {
        $this->commandUtilityFacade = $commandUtilityFacade;
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * @api
     */
    public function getProjectVersion(): ProjectVersion
    {
        $projectVersion = GeneralUtility::makeInstance(ProjectVersion::class);

        switch ($this->extensionConfiguration->getMode()) {
            case ProjectVersionModeEnumeration::STATIC_VERSION:
                $this->setStaticVersion($projectVersion);
                break;
            case ProjectVersionModeEnumeration::GIT:
                $this->setVersionFromGit($projectVersion);
                break;
            case ProjectVersionModeEnumeration::GIT_FILE_FALLBACK:
                $this->setVersionFromGit($projectVersion);

                if ($projectVersion->getVersion() === ProjectVersion::UNKNOWN_VERSION) {
                    //if version is still unknown, try to resolve version by file
                    $this->setVersionFromFile($projectVersion);
                }
                break;
            case ProjectVersionModeEnumeration::FILE:
            default:
                $this->setVersionFromFile($projectVersion);
        }

        return $projectVersion;
    }

    private function formatVersion($revision, $tag, $branch): string
    {
        switch ($this->extensionConfiguration->getGitFormat()) {
            case GitCommandEnumeration::FORMAT_REVISION:
                $format = $revision;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_TAG:
                $format = sprintf('[%s] %s', $revision, $tag);
                break;
            case GitCommandEnumeration::FORMAT_BRANCH:
                $format = $branch;
                break;
            case GitCommandEnumeration::FORMAT_TAG:
                $format = $tag;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_BRANCH:
            default:
                $format = sprintf('[%s] %s', $revision, $branch);
        }

        return $format;
    }

    private function isGitAvailable(): bool
    {
        return $this->isExecEnabled() &&
            // check if git exists
            $this->commandUtilityFacade->exec('git --version', $_, $returnCode) &&
            $returnCode === 0;
    }

    private function setStaticVersion(ProjectVersion $projectVersion): void
    {
        $projectVersion->setVersion($this->extensionConfiguration->getStaticVersion());
    }

    private function setVersionFromFile(ProjectVersion $projectVersion): void
    {
        $versionFilePath = $this->extensionConfiguration->getAbsVersionFilePath();
        if (file_exists($versionFilePath)) {
            $versionFileContent = file_get_contents($versionFilePath);
            $projectVersion->setVersion($versionFileContent);
        }
    }

    private function setVersionFromGit(ProjectVersion $projectVersion): void
    {
        if (!$this->isGitAvailable()) {
            return;
        }

        $version = $this->getVersionByFormat();

        if (!empty($version)) {
            $gitIconIdentifier = 'information-git';

            $projectVersion->setVersion($version);
            $projectVersion->setIconIdentifier($gitIconIdentifier);
        }
    }

    private function getVersionByFormat(): string
    {
        $branch = trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_BRANCH));
        $revision = trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_REVISION));
        $tag = trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_TAG));
        $format = '';

        if ($branch || $revision || $tag) {
            $format = $this->formatVersion($revision, $tag, $branch);
        }

        return $format;
    }

    private function isExecEnabled(): bool
    {
        return in_array('exec', GeneralUtility::trimExplode(',', ini_get('disable_functions')));
    }
}
