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

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'information-project-version' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:project_version/Resources/Public/Icons/ToolbarItem.svg',
    ],
];
