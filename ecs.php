<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
        __DIR__ . '/ecs.php',
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::COMMON,
    ]);

    $ecsConfig->skip([
        \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class, // this guy mixes everything into a single mess. so we need to do this manually
    ]);
};
