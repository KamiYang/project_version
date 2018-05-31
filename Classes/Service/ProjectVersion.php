<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class ProjectVersion
 *
 * @package KamiYang\ProjectVersion\Service
 * @author Jan Stockfisch <jan.stockfisch@googlemail.com>
 */
class ProjectVersion implements SingletonInterface
{
    const UNKNOWN_VERSION = 'Unknown project version';

    /**
     * @var string $title
     */
    protected $title = 'Project Version';

    /**
     * @var string $version
     */
    protected $version = self::UNKNOWN_VERSION;

    /**
     * @var string
     * @todo default icon for version file
     */
    protected $iconIdentifier = '';

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
