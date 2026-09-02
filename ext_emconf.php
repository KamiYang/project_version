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

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Version',
    'description' => "Displays current project version based on 'VERSION' file or GIT revision.",
    'category' => 'misc',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Jan Stockfisch',
    'author_email' => 'jan@jan-stockfisch.de',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.99.99',
            'typo3' => '13.4.0-15.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
