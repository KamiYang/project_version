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
 * How the project version is resolved. Values match the options in ext_conf_template.txt.
 */
enum ProjectVersionMode: string
{
    case File = '0';
    case Git = '1';
    case GitFileFallback = '2';
    case StaticVersion = '3';
}
