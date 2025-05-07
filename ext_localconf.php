<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \GeorgRinger\FavoriteContent\Hooks\CreationEnricher::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Controller\ContentElement\NewContentElementController::class] = [
    'className' => \GeorgRinger\FavoriteContent\Xclass\XclassedNewContentElementController::class,
];
