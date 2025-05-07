<?php

return [
    'ctrl' => [
        'title' => 'Favorite Content Item',
        'label' => 'name',
        'label_alt' => 'type',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'hideAtCopy' => true,
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'type' => 'type',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            \GeorgRinger\FavoriteContent\Enum\EnumType::New->value => 'extension-favorite-type-new',
            \GeorgRinger\FavoriteContent\Enum\EnumType::Copy->value => 'extension-favorite-type-copy',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        \GeorgRinger\FavoriteContent\Enum\EnumType::New->value => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                   --palette--;;paletteGeneral,--palette--;;paletteNew,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
          ',
        ],
        \GeorgRinger\FavoriteContent\Enum\EnumType::Copy->value => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;paletteGeneral,--palette--;;paletteCopy,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
        ],
    ],
    'palettes' => [
        'paletteGeneral' => [
            'showitem' => 'type,--linebreak--,name,--linebreak--,description',
        ],
        'paletteNew' => [
            'label' => 'Configuration',
            'showitem' => 'content_type, save_and_close',
        ],
        'paletteCopy' => [
            'label' => 'Configuration',
            'showitem' => 'record',
        ],
    ],
    'columns' => [
        'name' => [
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'type' => [
            'label' => 'Type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['value' => \GeorgRinger\FavoriteContent\Enum\EnumType::New->value, 'label' => 'New', 'icon' => 'extension-favorite-type-new'],
                    ['value' => \GeorgRinger\FavoriteContent\Enum\EnumType::Copy->value, 'label' => 'Copy', 'icon' => 'extension-favorite-type-copy'],
                ],
                'maxitems' => 1,
            ],
        ],
        'description' => [
            'label' => 'Description',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'cols' => 30,
                'rows' => 2,
            ],
        ],
        'save_and_close' => [
            'label' => 'Save and close',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'record' => [
            'label' => 'Record to copy',
            'config' => [
                'type' => 'group',
                'allowed' => 'tt_content',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
            ],
        ],
        'content_type' => [
            'label' => 'Type',
            'config' => [],
        ],
        'cruser' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'direct' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
