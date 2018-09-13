<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class ProjectVersion.
 *
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
class ProjectVersion implements SingletonInterface
{
    const UNKNOWN_VERSION = 'Unknown project version';

    /**
     * @var string
     */
    protected $title = 'Project Version';

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
        return $this->version;
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
