<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Version',
    'description' => 'Displays current project version based on \'VERSION\' file or GIT revision.',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Jan Stockfisch',
    'author_email' => 'jan@jan-stockfisch.de',
    'version' => '0.5.0',
    'constraints' => [
        'depends' => [
            'php' => '7.0',
            'typo3' => '8.7.0-9.4.0'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ]
    ]
];
