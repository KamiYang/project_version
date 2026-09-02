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
use KamiYang\ProjectVersion\Enumeration\GitCommand;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionMode;
use KamiYang\ProjectVersion\Facade\CommandUtilityFacade;

/**
 * Resolves the current project version based on the extension settings.
 */
class ProjectVersionService
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly CommandUtilityFacade $commandUtilityFacade,
    ) {}

    /**
     * @api
     */
    public function getProjectVersion(): ProjectVersion
    {
        $projectVersion = new ProjectVersion();

        switch ($this->extensionConfiguration->getMode()) {
            case ProjectVersionMode::StaticVersion:
                $this->setStaticVersion($projectVersion);
                break;
            case ProjectVersionMode::Git:
                $this->setVersionFromGit($projectVersion);
                break;
            case ProjectVersionMode::GitFileFallback:
                $this->setVersionFromGit($projectVersion);
                if ($projectVersion->isUnknown()) {
                    $this->setVersionFromFile($projectVersion);
                }
                break;
            case ProjectVersionMode::File:
                $this->setVersionFromFile($projectVersion);
                break;
        }

        return $projectVersion;
    }

    protected function isGitAvailable(): bool
    {
        if (!$this->commandUtilityFacade->isExecAvailable()) {
            return false;
        }

        $returnCode = 1;
        $this->commandUtilityFacade->exec(GitCommand::Version->value, $output, $returnCode);

        return $returnCode === 0;
    }

    private function setStaticVersion(ProjectVersion $projectVersion): void
    {
        $staticVersion = $this->extensionConfiguration->getStaticVersion();
        if ($staticVersion !== '') {
            $projectVersion->setVersion($staticVersion);
        }
    }

    private function setVersionFromFile(ProjectVersion $projectVersion): void
    {
        $versionFilePath = $this->extensionConfiguration->getAbsVersionFilePath();
        if ($versionFilePath === '' || !is_file($versionFilePath)) {
            return;
        }

        $content = trim((string)file_get_contents($versionFilePath));
        if ($content !== '') {
            $projectVersion->setVersion($content);
        }
    }

    private function setVersionFromGit(ProjectVersion $projectVersion): void
    {
        if (!$this->isGitAvailable()) {
            return;
        }

        $version = $this->getVersionByFormat();
        if ($version !== '') {
            $projectVersion->setVersion($version);
            $projectVersion->setIconIdentifier(ProjectVersion::GIT_ICON_IDENTIFIER);
        }
    }

    private function getVersionByFormat(): string
    {
        $branch = trim($this->commandUtilityFacade->exec(GitCommand::Branch->value));
        $revision = trim($this->commandUtilityFacade->exec(GitCommand::Revision->value));
        $tag = trim($this->commandUtilityFacade->exec(GitCommand::Tag->value));

        if ($branch === '' && $revision === '' && $tag === '') {
            return '';
        }

        return $this->extensionConfiguration->getGitFormat()->format($revision, $branch, $tag);
    }
}
