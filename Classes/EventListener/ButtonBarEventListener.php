<?php

declare(strict_types=1);

namespace GeorgRinger\PinnedContent\EventListener;

use GeorgRinger\PinnedContent\Repository\FavoriteRepository;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Page\PageRenderer;

#[AsEventListener]
readonly final class ButtonBarEventListener
{
    public function __construct(
        private IconFactory $iconFactory,
        private FavoriteRepository $favoriteRepository,
        private PageRenderer $pageRenderer,
    ) {}

    public function __invoke(ModifyButtonBarEvent $event): void
    {
        $request = $this->getRequest();

        $editVars = (array) ($request->getQueryParams()['edit']['tt_content'] ?? []);
        if (empty($editVars)) {
            return;
        }
        $action = array_values($editVars)[0] ?? '';
        if ($action !== 'edit') {
            return;
        }

        $recordId = (int) (array_keys($editVars)[0] ?? 0);
        if ($recordId === 0) {
            return;
        }

        if (!$this->userHasWriteAccess()) {
            return;
        }

        $this->pageRenderer->loadJavaScriptModule('@georgringer/pinned-content/toggle.js');


        $row = $this->favoriteRepository->getByUid($recordId);

        $button = $event->getButtonBar()->makeLinkButton();
        $button
            ->setTitle('Pinned')
            ->setDataAttributes(['pinned-button' => 1, 'pinned-content-id' => $recordId])
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon($row ? 'extension-pinned-pin-filled' : 'extension-pinned-pin', IconSize::SMALL))
            ->setHref('#');

        $buttons = $event->getButtons();
        $buttons[ButtonBar::BUTTON_POSITION_RIGHT][2][] = $button;
        $event->setButtons($buttons);
    }


    private function userHasWriteAccess(): bool
    {
        return $this->getBackendUser()->check('tables_modify', 'tx_pinned_content_item');
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    private function getRequest(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
