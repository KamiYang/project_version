<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Backend\ToolbarItems;

use KamiYang\ProjectVersion\Facade\LocalizationUtilityFacade;
use KamiYang\ProjectVersion\Service\ProjectVersionService;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProjectVersionSlot
 *
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
final class ProjectVersionSlot implements SingletonInterface
{
    /**
     * @param \TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem $pObj
     */
    public function getProjectVersion(SystemInformationToolbarItem $pObj)
    {
        $projectVersion = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ProjectVersionService::class)
            ->getProjectVersion();

        $version = $projectVersion->getVersion();

        if (StringUtility::beginsWith($version, 'LLL:')) {
            $version = GeneralUtility::makeInstance(LocalizationUtilityFacade::class)->translate($version);
        }

        $pObj->addSystemInformation(
            $projectVersion->getTitle(),
            $version,
            $projectVersion->getIconIdentifier()
        );
    }
}
