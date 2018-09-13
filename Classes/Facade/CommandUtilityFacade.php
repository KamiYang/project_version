<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Facade;

use TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * Class CommandUtilityFacade
 *
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class CommandUtilityFacade
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
    public function exec($command, &$output = null, &$returnValue = 0)
    {
        return CommandUtility::exec($command, $output, $returnValue);
    }
}
