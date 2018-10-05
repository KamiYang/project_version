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
use KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionService
 */
class ProjectVersionService implements SingletonInterface
{
    /**
     * @var \KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade
     */
    protected $systemEnvironmentBuilderFacade;
    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    protected $commandUtilityFacade;

    /**
     * @api
     */
    public function getProjectVersion(): ProjectVersion
    {
        $projectVersion = GeneralUtility::makeInstance(ProjectVersion::class);

        switch (ExtensionConfiguration::getMode()) {
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

    /**
     * @param $revision
     * @param $tag
     * @param $branch
     * @return string
     */
    private function formatVersionBasedOnConfiguration($revision, $tag, $branch): string
    {
        switch (ExtensionConfiguration::getGitFormat()) {
            case GitCommandEnumeration::FORMAT_REVISION:
                $format = $revision;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_TAG:
                $format = \sprintf('[%s] %s', $revision, $tag);
                break;
            case GitCommandEnumeration::FORMAT_BRANCH:
                $format = $branch;
                break;
            case GitCommandEnumeration::FORMAT_TAG:
                $format = $tag;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_BRANCH:
            default:
                $format = \sprintf('[%s] %s', $revision, $branch);
        }

        return $format;
    }

    /**
     * SystemEnvironmentBuilderFacade injector.
     *
     * @param \KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade $systemEnvironmentBuilderFacade
     */
    public function injectSystemEnvironmentBuilderFacade(SystemEnvironmentBuilderFacade $systemEnvironmentBuilderFacade)
    {
        $this->systemEnvironmentBuilderFacade = $systemEnvironmentBuilderFacade;
    }

    /**
     * CommandUtilityFacade injector.
     *
     * @param \KamiYang\ProjectVersion\Facade\CommandUtilityFacade $commandUtilityFacade
     */
    public function injectCommandUtilityFacade(CommandUtilityFacade $commandUtilityFacade)
    {
        $this->commandUtilityFacade = $commandUtilityFacade;
    }

    /**
     * @return bool
     */
    protected function isGitAvailable(): bool
    {
        return $this->systemEnvironmentBuilderFacade->isFunctionDisabled('exec') === false &&
            // check if git exists
            $this->commandUtilityFacade->exec('git --version', $_, $returnCode) &&
            $returnCode === 0;
    }

    /**
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setStaticVersion(ProjectVersion $projectVersion)
    {
        $projectVersion->setVersion(ExtensionConfiguration::getStaticVersion());
    }

    /**
     * Resolve version by common VERSION-file.
     *
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setVersionFromFile(ProjectVersion $projectVersion)
    {
        $versionFilePath = ExtensionConfiguration::getAbsVersionFilePath();
        if (\file_exists($versionFilePath)) {
            $versionFileContent = \file_get_contents($versionFilePath);
            $projectVersion->setVersion($versionFileContent);
        }
    }

    /**
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setVersionFromGit(ProjectVersion $projectVersion)
    {
        if ($this->isGitAvailable() === false) {
            return;
        }

        $version = $this->getVersionByFormat();

        if (!empty($version)) {
            /*
             * The icon identifier for "git" changed between TYPO3 v8 and v9.
             * For TYPO3 v8 it's "sysinfo-git" and for v9 it's "information-git"
             */
            $gitIconIdentifier = (float)TYPO3_version < 9 ? 'sysinfo-git' : 'information-git';

            $projectVersion->setVersion($version);
            $projectVersion->setIconIdentifier($gitIconIdentifier);
        }
    }

    /**
     * @return string
     */
    private function getVersionByFormat(): string
    {
        $branch = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_BRANCH));
        $revision = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_REVISION));
        $tag = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_TAG));
        $format = '';

        if ($branch || $revision || $tag) {
            $format = $this->formatVersionBasedOnConfiguration($revision, $tag, $branch);
        }

        return $format;
    }
}
