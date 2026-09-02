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

$header = <<<'HEADER'
This file is part of the ProjectVersion project.

It is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

For the full copyright and license information, please read
LICENSE file that was distributed with this source code.
HEADER;

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->setHeader($header, true);
$config->getFinder()
    ->in(__DIR__ . '/Classes')
    ->in(__DIR__ . '/Configuration')
    ->in(__DIR__ . '/Tests')
    ->append([__FILE__, __DIR__ . '/ext_emconf.php', __DIR__ . '/rector.php']);
$config->setCacheFile(__DIR__ . '/.Build/.php-cs-fixer.cache');

return $config;
