<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Enumeration;

/**
 * Class ProjectVersionModeEnumeration
 *
 * @package KamiYang\ProjectVersion\Enumeration
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
final class ProjectVersionModeEnumeration
{
    const FILE = '0';
    const GIT = '1';
    const GIT_FILE_FALLBACK = '2';
}
