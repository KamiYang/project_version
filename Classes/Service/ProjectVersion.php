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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class ProjectVersion
 */
class ProjectVersion implements SingletonInterface
{
    public const UNKNOWN_VERSION = self::LLL . ':toolbarItems.sysinfo.project-version.unknown';
    private const LLL = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf';

    /**
     * @var string $title
     */
    protected $title = self::LLL . ':toolbarItems.sysinfo.project-version';

    /**
     * @var string $version
     */
    protected $version = self::UNKNOWN_VERSION;

    /**
     * @var string
     */
    protected $iconIdentifier = 'information-project-version';

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getIconIdentifier(): string
    {
        return $this->iconIdentifier;
    }

    /**
     * @param string $iconIdentifier
     */
    public function setIconIdentifier(string $iconIdentifier): void
    {
        $this->iconIdentifier = $iconIdentifier;
    }
}
