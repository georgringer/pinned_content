<?php
declare(strict_types=1);

namespace GeorgRinger\PinnedContent\Hooks;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;

class TcaItemsProcessorFunctions
{

    public function populateCtypes(array &$fieldDefinition): void
    {
        $user = $this->getBackendUser();

        $fieldDefinition['items'] = array_filter(
            $fieldDefinition['items'],
            static fn(SelectItem $item): bool => $user->checkAuthMode('tt_content', 'CType', $item->getValue())
        );
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
