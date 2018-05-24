<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Version',
    'description' => 'Displays current project version based on \'VERSION\' file or GIT revision.',
    'category' => 'misc',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Jan Stockfisch',
    'author_email' => 'jan.stockfisch@googlemail.com',
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'php' => '7.1.0-7.1.999',
            'typo3' => '8.7-8.7.999'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ]
    ]
];
