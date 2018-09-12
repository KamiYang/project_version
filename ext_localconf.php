<?php
defined('TYPO3_MODE') or die();

call_user_func(function (string $extKey) {
    if (TYPO3_MODE === 'BE') {
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

        // Add project version to SystemInformation
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->connect(
            \TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem::class,
            'getSystemInformation',
            \KamiYang\ProjectVersion\Backend\ToolbarItems\ProjectVersionSlot::class,
            'getProjectVersion'
        );
    }
}, 'project_version');
