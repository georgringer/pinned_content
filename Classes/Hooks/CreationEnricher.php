<?php

declare(strict_types=1);

namespace GeorgRinger\PinnedContent\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;

class CreationEnricher
{
    public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, string $table, $id, DataHandler $dataHandler): void
    {
        // Not within pinned
        if ($table !== 'tx_pinned_content_item') {
            return;
        }
        if (isset($incomingFieldArray['cruser'])) {
            return;
        }
        $incomingFieldArray['cruser'] = $dataHandler->BE_USER->user['uid'] ?? 0;
    }
}
