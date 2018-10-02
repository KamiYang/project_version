<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Enumeration;

/*
 * This file is part of the ProjectVersion project.
 *
 * It is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

/**
 * Class GitCommandEnumeration
 *
 * @author Jan Stockfisch <jan@jan-stockfisch.de>
 */
final class GitCommandEnumeration
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
