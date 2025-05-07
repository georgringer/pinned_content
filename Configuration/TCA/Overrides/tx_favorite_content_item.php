<?php

$GLOBALS['TCA']['tx_favorite_content_item']['columns']['content_type']['config'] =
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config'];

$GLOBALS['TCA']['tx_favorite_content_item']['columns']['content_type']['config']['itemsProcFunc']
    = \GeorgRinger\FavoriteContent\Hooks\TcaItemsProcessorFunctions::class . '->populateCtypes';
unset($GLOBALS['TCA']['tx_favorite_content_item']['columns']['content_type']['config']['authMode']);
