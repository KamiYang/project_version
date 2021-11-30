<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Version',
    'description' => 'Displays current project version based on a \'VERSION\' file or GIT revision.',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Jan Stockfisch',
    'author_email' => 'jan@jan-stockfisch.de',
    'version' => '2.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.0',
            'typo3' => '10.4.0-11.5.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ]
    ]
];
