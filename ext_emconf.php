<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Pinned Content',
    'description' => 'Enhances the TYPO3 content element wizard with pinned items, templates, and personal pin lists for faster editing.',
    'category' => 'be',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'classmap' => ['Classes'],
    ],
];
