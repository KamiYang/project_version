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

namespace KamiYang\ProjectVersion\Service;

/**
 * Value object holding the resolved project version and how it is displayed.
 */
final class ProjectVersion
{
    public const UNKNOWN_VERSION = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown';

    public const DEFAULT_TITLE = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version';

    public const DEFAULT_ICON_IDENTIFIER = 'information-project-version';

    public const GIT_ICON_IDENTIFIER = 'information-git';

    private string $title = self::DEFAULT_TITLE;

    private string $version = self::UNKNOWN_VERSION;

    private string $iconIdentifier = self::DEFAULT_ICON_IDENTIFIER;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function isUnknown(): bool
    {
        return $this->version === self::UNKNOWN_VERSION;
    }

    public function getIconIdentifier(): string
    {
        return $this->iconIdentifier;
    }

    public function setIconIdentifier(string $iconIdentifier): void
    {
        $this->iconIdentifier = $iconIdentifier;
    }
}
