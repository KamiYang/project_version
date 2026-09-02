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

use Rector\Config\RectorConfig;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
        __DIR__ . '/ext_emconf.php',
        __DIR__ . '/rector.php',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/Tests/Fixture',

        // typo3/coding-standards enforces self::assert*() (php_unit_test_case_static_method_calls), do not fight it
        PreferPHPUnitThisCallRector::class,
        // "$event" is the conventional parameter name for PSR-14 listeners
        RenameParamToMatchTypeRector::class => [
            __DIR__ . '/Classes/Backend/EventListener',
        ],
    ]);

    // Lowest supported PHP version of the extension (TYPO3 v13/v14)
    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        DowngradeLevelSetList::DOWN_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,

        // Symfony rules (version based sets are derived from the installed symfony packages since Rector 1.x)
        SymfonySetList::COMPOSER_BASED,
        SymfonySetList::SYMFONY_CODE_QUALITY,

        // PHPUnit rules (version based sets are derived from the installed phpunit since Rector 1.x;
        // PHPUNIT_EXCEPTION, PHPUNIT_SPECIFIC_METHOD and PHPUNIT_YIELD_DATA_PROVIDER are part of PHPUNIT_CODE_QUALITY)
        PHPUnitSetList::COMPOSER_BASED,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_MOCK_TO_STUB,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);
};
