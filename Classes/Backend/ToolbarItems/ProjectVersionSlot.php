<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Backend\ToolbarItems;

use KamiYang\ProjectVersion\Service\ProjectVersionService;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionSlot
 *
 * @package KamiYang\ProjectVersion\Backend\ToolbarItems
 * @author Jan Stockfisch <jan.stockfisch@googlemail.com>
 */
final class ProjectVersionSlot implements SingletonInterface
{
    /**
     * @param \TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem $pObj
     */
    public function getProjectVersion(SystemInformationToolbarItem $pObj)
    {
        $projectVersion = GeneralUtility::makeInstance(ProjectVersionService::class)
            ->getProjectVersion();

        $pObj->addSystemInformation(
            $projectVersion->getTitle(),
            $projectVersion->getVersion(),
            '' //@todo create an icon
        );
    }
}
