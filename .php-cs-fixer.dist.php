<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
    ->in(__DIR__ . '/Classes')
    ->in(__DIR__ . '/Configuration')
    ->in(__DIR__ . '/Tests')
    ->append([__FILE__, __DIR__ . '/ext_emconf.php', __DIR__ . '/rector.php']);
$config->setCacheFile(__DIR__ . '/.Build/.php-cs-fixer.cache');

return $config;
