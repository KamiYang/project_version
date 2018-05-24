<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProjectVersionService
 *
 * @package KamiYang\ProjectVersion\Service
 * @author Jan Stockfisch <jan.stockfisch@googlemail.com>
 */
class ProjectVersionService implements SingletonInterface
{
    /**
     * @api
     */
    public function getProjectVersion(): ProjectVersion
    {
        $projectVersion = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ProjectVersion::class);

        $versionFilePath = GeneralUtility::getFileAbsFileName(PATH_site . 'VERSION');
        if (\file_exists($versionFilePath)) {
            $versionFileContent = \file_get_contents($versionFilePath);
            $projectVersion->setVersion($versionFileContent);
        }

        return $projectVersion;
    }
}
