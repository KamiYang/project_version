<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Enumeration;

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * Class GitCommandEnumeration
 *
 * @package KamiYang\ProjectVersion\Enumeration
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
final class GitCommandEnumeration extends Enumeration
{
    const CMD_BRANCH = 'git rev-parse --abbrev-ref HEAD';
    const CMD_REVISION = 'git rev-parse --short HEAD';
    const CMD_TAG = 'git describe --tags';

    const FORMAT_REVISION = '0';
    const FORMAT_REVISION_BRANCH = '1';
    const FORMAT_REVISION_TAG = '2';
    const FORMAT_BRANCH = '3';
    const FORMAT_TAG = '4';
}
