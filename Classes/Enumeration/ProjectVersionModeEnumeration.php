<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Enumeration;

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * Class ProjectVersionModeEnumeration
 *
 * @package KamiYang\ProjectVersion\Enumeration
 * @author Jan Stockfisch <jan.stockfisch@googlemail.com>
 */
final class ProjectVersionModeEnumeration extends Enumeration
{
    const __default = self::FILE;

    const FILE = '0';
    const GIT = '1';
    const GIT_FILE_FALLBACK = '2';
}
