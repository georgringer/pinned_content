<?php

$GLOBALS['TCA']['tx_pinned_content_item']['columns']['content_type']['config'] =
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config'];

$GLOBALS['TCA']['tx_pinned_content_item']['columns']['content_type']['config']['itemsProcFunc']
    = \GeorgRinger\PinnedContent\Hooks\TcaItemsProcessorFunctions::class . '->populateCtypes';
unset($GLOBALS['TCA']['tx_pinned_content_item']['columns']['content_type']['config']['authMode']);
