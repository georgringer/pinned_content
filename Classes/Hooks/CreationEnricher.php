<?php

declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\MathUtility;

class CreationEnricher
{
    public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, string $table, $id, DataHandler $dataHandler)
    {
        // Not within sys_note
        if ($table !== 'tx_favorite_content_item') {
            return;
        }
        if (isset($incomingFieldArray['cruser'])) {
            return;
        }
        $incomingFieldArray['cruser'] = $dataHandler->BE_USER->user['uid'] ?? 0;
    }
}
