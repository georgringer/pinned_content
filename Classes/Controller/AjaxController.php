<?php
declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\Controller;

use GeorgRinger\FavoriteContent\Repository\FavoriteRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Http\JsonResponse;

#[AsController]
class AjaxController
{

    public function __construct(
        private readonly FavoriteRepository $favoriteRepository,
    ) {}

    public function toggleAction(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = (int) ($request->getQueryParams()['id'] ?? 0);
        if ($identifier === 0) {
            return new JsonResponse(['error' => 'no id given']);
        }

        $row = $this->favoriteRepository->getByUid($identifier);

        $result = [];
        if (!$row) {
            $this->favoriteRepository->add($identifier);
            $result['title'] = 'Added to favorites';
            $result['icon'] = 'content-heart';
        } else {
            $this->favoriteRepository->remove($row['uid']);
            $result['title'] = 'Removed from favorites';
            $result['icon'] = 'actions-heart';
        }

        return new JsonResponse(['result' => $result]);
    }
}
