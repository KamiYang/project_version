<?php

declare(strict_types=1);

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
