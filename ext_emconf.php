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
    'version' => '2.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '^7.0',
            'typo3' => '9.5.0-10.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ]
    ]
];
