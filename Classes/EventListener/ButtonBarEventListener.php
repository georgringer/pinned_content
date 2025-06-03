<?php

declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\EventListener;

use GeorgRinger\FavoriteContent\Repository\FavoriteRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

#[AsEventListener()]
final class ButtonBarEventListener
{
    public function __construct(
        private readonly IconFactory $iconFactory,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly PageRenderer $pageRenderer,
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

        $this->pageRenderer->loadJavaScriptModule('@georgringer/favorite-content/toggle.js');


        $row = $this->favoriteRepository->getByUid($recordId);

        $button = $event->getButtonBar()->makeLinkButton();
        $button
            ->setTitle('Favorite')
            ->setClasses('favorite-js')
            ->setDataAttributes(['favorite' => $recordId])
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon($row ? 'content-heart' : 'actions-heart', IconSize::SMALL))
            ->setHref('#');

        $buttons = $event->getButtons();
        $buttons[ButtonBar::BUTTON_POSITION_RIGHT][2][] = $button;
        $event->setButtons($buttons);
    }

    private function getRequest(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
