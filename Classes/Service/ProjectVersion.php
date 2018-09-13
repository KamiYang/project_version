<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use KamiYang\ProjectVersion\Facade\LocalizationUtilityFacade;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Class ProjectVersion.
 *
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
class ProjectVersion implements SingletonInterface
{
    const UNKNOWN_VERSION = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown';

    /**
     * @var string
     */
    protected $title = 'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version';

    /**
     * @var string
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
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        $version = $this->version;

        if (StringUtility::beginsWith($version, 'LLL:')) {
            $version = GeneralUtility::makeInstance(LocalizationUtilityFacade::class)->translate($this->version);
        }

        return $version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
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
    public function setIconIdentifier(string $iconIdentifier)
    {
        $this->iconIdentifier = $iconIdentifier;
    }
}
