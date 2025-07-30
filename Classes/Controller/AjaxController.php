<?php
declare(strict_types=1);

namespace GeorgRinger\PinnedContent\Controller;

use GeorgRinger\PinnedContent\Repository\FavoriteRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;

#[AsController]
readonly class AjaxController
{

    public function __construct(
        private FavoriteRepository $favoriteRepository,
    )
    {
    }

    public function toggleAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = (int)($request->getQueryParams()['id'] ?? 0);
        if ($identifier === 0) {
            return new JsonResponse(['error' => 'no id given']);
        }

        $row = $this->favoriteRepository->getByUid($identifier);

        $result = [];
        if (!$row) {
            $this->favoriteRepository->add($identifier);
            $result['title'] = $this->getLanguageService()->sL('LLL:EXT:pinned_content/Resources/Private/Language/locallang.xlf:confirmation.add');
            $result['icon'] = 'extension-pinned-pin-filled';
        } else {
            $this->favoriteRepository->remove($row['uid']);
            $result['title'] = $this->getLanguageService()->sL('LLL:EXT:pinned_content/Resources/Private/Language/locallang.xlf:confirmation.remove');
            $result['icon'] = 'extension-pinned-pin';
        }

        return new JsonResponse(['result' => $result]);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
