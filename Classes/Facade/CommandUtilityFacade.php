<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Facade;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * Class CommandUtilityFacade
 * Only used for mocking purposes. This is a wrapper for the actual Utility.
 *
 * @see \TYPO3\CMS\Core\Utility\CommandUtility
 * @internal
 * @package KamiYang\ProjectVersion\Facade
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class CommandUtilityFacade implements SingletonInterface
{
    /**
     * Wrapper function for php exec function
     * Needs to be central to have better control and possible fix for issues
     *
     * @param string $command
     * @param array|null $output
     * @param int $returnValue
     * @return string
     */
    public function exec($command, &$output = null, &$returnValue = 0): string
    {
        return CommandUtility::exec($command, $output, $returnValue);
    }
}
