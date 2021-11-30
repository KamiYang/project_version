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
 * Class ProjectVersionModeEnumeration
 */
final class ProjectVersionModeEnumeration
{
    public const FILE = '0';
    public const GIT = '1';
    public const GIT_FILE_FALLBACK = '2';
    public const STATIC_VERSION = '3';
}
