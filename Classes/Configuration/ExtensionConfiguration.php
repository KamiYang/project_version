<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Configuration;

use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Class ExtensionConfiguration.
 *
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
final class ExtensionConfiguration implements SingletonInterface
{
    /**
     * Extension configuration.
     *
     * @var array
     */
    private static $configuration = [];

    /**
     * Relative file path of the VERSION-file. Blank equals const 'PATH_site'.
     *
     * @var string
     */
    private static $versionFilePath = '';

    /**
     * Indicator for the fetching method.
     *
     * @var string
     *
     * @see \KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration
     */
    private static $mode = ProjectVersionModeEnumeration::FILE;

    /**
     * @var string
     */
    private static $gitFormat = '';

    /**
     * Fetch absolute version filename.
     *
     * @return string
     */
    public static function getAbsVersionFilePath(): string
    {
        return GeneralUtility::getFileAbsFileName(self::getVersionFilePath());
    }

    /**
     * @return string
     */
    public static function getVersionFilePath(): string
    {
        return self::$versionFilePath;
    }

    /**
     * @return string
     */
    public static function getMode(): string
    {
        return self::$mode;
    }

    /**
     * @return string
     */
    public static function getGitFormat(): string
    {
        return self::$gitFormat;
    }

    public function __construct()
    {
        self::$configuration = $this->getExtensionConfigurationFromGlobals();

        self::$versionFilePath = $this->resolveVersionFilePath();
        self::$mode = self::$configuration['mode'];
        self::$gitFormat = self::$configuration['gitFormat'];
    }

    /**
     * @return array
     */
    private function getExtensionConfigurationFromGlobals(): array
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['project_version'];

        if (is_string($configuration)) {
            $configuration = @unserialize($configuration);
        }

        return $configuration ?? [];
    }

    /**
     * @return string
     */
    private function resolveVersionFilePath(): string
    {
        $pathFromConfiguration = self::$configuration['versionFilePath'] ?? '';

        if (empty($pathFromConfiguration) || $this->isDirectory($pathFromConfiguration)) {
            $pathFromConfiguration .= 'VERSION';
        }

        return $pathFromConfiguration;
    }

    /**
     * @param string $pathFromConfiguration
     *
     * @return bool
     */
    private function isDirectory(string $pathFromConfiguration): bool
    {
        return StringUtility::endsWith($pathFromConfiguration, '/') === true;
    }
}
