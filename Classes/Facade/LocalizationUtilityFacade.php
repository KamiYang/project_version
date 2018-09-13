<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Facade;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class LocalizationUtilityFacade
 *
 * Facade object for testing purposes of TYPO3\CMS\Extbase\Utility\LocalizationUtility.
 *
 * @see \TYPO3\CMS\Extbase\Utility\LocalizationUtility
 * @internal
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
class LocalizationUtilityFacade
{
    /**
     * Returns the localized label of the LOCAL_LANG key, $key.
     *
     * @param string $key The key from the LOCAL_LANG array for which to return the value.
     * @param string|null $extensionName The name of the extension
     * @param array $arguments The arguments of the extension, being passed over to vsprintf
     * @param string $languageKey The language key or null for using the current language from the system
     * @param string[] $alternativeLanguageKeys The alternative language keys if no translation was found. If null and we are in the frontend, then the language_alt from TypoScript setup will be used
     * @return string|null The value from LOCAL_LANG or null if no translation was found.
     */
    public function translate(
        $key,
        $extensionName = null,
        $arguments = null,
        string $languageKey = null,
        array $alternativeLanguageKeys = null
    ) {
        return LocalizationUtility::translate($key, $extensionName, $arguments, $languageKey, $alternativeLanguageKeys);
    }
}
