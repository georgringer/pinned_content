<?php

declare(strict_types=1);

use GeorgRinger\PinnedContent\Enum\EnumPosition;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$lll = 'LLL:EXT:pinned_content/Resources/Private/Language/locallang_db.xlf:';

$GLOBALS['TYPO3_USER_SETTINGS']['columns']['pinned_content.wizardPosition'] = [
    'label' => $lll . 'userSettings.wizardPosition',
    'type' => 'select',
    'items' => [
        EnumPosition::Top->value => $lll . 'userSettings.wizardPosition.top',
        EnumPosition::Bottom->value => $lll . 'userSettings.wizardPosition.bottom',
    ],
    'default' => EnumPosition::Bottom->value,
];

ExtensionManagementUtility::addFieldsToUserSettings(
    '--div--;' . $lll . 'userSettings.tab,pinned_content.wizardPosition',
);