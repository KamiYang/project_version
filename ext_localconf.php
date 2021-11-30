<?php

defined('TYPO3_MODE') or die();

(static function () {
    if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_BE) {
        //Fetch ExtensionConfiguration
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \KamiYang\ProjectVersion\Configuration\ExtensionConfiguration::class
        );

        // Register custom icon
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)
            ->registerIcon(
                'information-project-version',
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:project_version/Resources/Public/Icons/ToolbarItem.svg'
                ]
            );
    }
})();
