<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Version',
    'description' => 'Displays current project version based on \'VERSION\' file or GIT revision.',
    'category' => 'misc',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Jan Stockfisch',
    'author_email' => 'jan.stockfisch@googlemail.com',
    'version' => '0.4.2-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.0',
            'typo3' => '8.7.0-8.7.19'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ]
    ]
];
