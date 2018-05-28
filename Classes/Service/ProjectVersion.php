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
    /**
     * @var string $title
     */
    protected $title = 'Project Version';
    /**
     * @var string $version
     */
    protected $version = 'Unknown project version';

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
}
