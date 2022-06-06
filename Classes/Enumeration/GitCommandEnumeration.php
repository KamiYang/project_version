<?php

declare(strict_types=1);

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

namespace KamiYang\ProjectVersion\Enumeration;

/**
 * Class GitCommandEnumeration
 */
final class GitCommandEnumeration
{
    public const CMD_BRANCH = 'git rev-parse --abbrev-ref HEAD';
    public const CMD_REVISION = 'git rev-parse --short HEAD';
    public const CMD_TAG = 'git describe --tags';

    public const FORMAT_REVISION = '0';
    public const FORMAT_REVISION_BRANCH = '1';
    public const FORMAT_REVISION_TAG = '2';
    public const FORMAT_BRANCH = '3';
    public const FORMAT_TAG = '4';
}
