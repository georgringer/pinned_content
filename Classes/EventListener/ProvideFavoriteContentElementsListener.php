<?php

declare(strict_types=1);

namespace GeorgRinger\PinnedContent\EventListener;

use GeorgRinger\PinnedContent\Enum\EnumType;
use GeorgRinger\PinnedContent\Repository\FavoriteRepository;
use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsEventListener]
readonly final class ProvideFavoriteContentElementsListener
{
    public function __construct(
        private UriBuilder $uriBuilder,
        private FavoriteRepository $favoriteRepository,
    ) {}

    protected string $returnUrl;

    public function __invoke(ModifyNewContentElementWizardItemsEvent $event): void
    {
        $items = $event->getWizardItems();
        $request = $GLOBALS['TYPO3_REQUEST'];
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $languageService = $this->getLanguageService();
        $this->returnUrl = GeneralUtility::sanitizeLocalUrl($parsedBody['returnUrl'] ?? $queryParams['returnUrl'] ?? '');

        $groupedFavorites = $this->getGroupedFavorites($event);
        foreach ($groupedFavorites as $type => $groupedItems) {
            $enum = EnumType::tryFrom($type);
            if ($enum === null) {
                continue;
            }
            $items['favorite' . $enum->name] = [
                'header' => $languageService->sL(sprintf('LLL:EXT:pinned_content/Resources/Private/Language/locallang.xlf:wizard.header.%s', strtolower($enum->name))),
            ];
            $items += $groupedItems;
        }
        $event->setWizardItems($items);
    }

    private function getGroupedFavorites(ModifyNewContentElementWizardItemsEvent $event): array
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $rows = $this->favoriteRepository->getAllOfCurrentUser();

        if (!$rows) {
            return [];
        }
        $items = [];
        foreach ($rows as $row) {
            $config = [
                'title' => $row['name'],
                'description' => $row['description'],
                'defaultValues' => [
                    'CType' => $row['content_type'],
                    '_favorite' => $row['uid'],
                ],
            ];
            $type = EnumType::tryFrom($row['type']);
            if ($type === null) {
                continue;
            }

            switch ($type) {
                case EnumType::Template:
                case EnumType::Pinned:
                    if (!$row['record']) {
                        continue 2;
                    }

                    $config['url'] = (string) $this->uriBuilder->buildUriFromRoute('tce_db', [
                        'cmd' => [
                            'tt_content' => [
                                $row['record'] => ['copy' => $event->getUidPid()],
                            ],
                        ],
                        'redirect' => $this->returnUrl,
                    ]);
                    $originalRow = BackendUtility::getRecord('tt_content', $row['record']);
                    $config['iconIdentifier'] = $iconFactory->mapRecordTypeToIconIdentifier('tt_content', $originalRow);

                    if ($row['direct']) {
                        $config['title'] = BackendUtility::getRecordTitle('tt_content', $originalRow) . ' | ' . BackendUtility::getProcessedValue('tt_content', 'CType', $originalRow['CType']);
                        $config['description'] = BackendUtility::getRecordPath($originalRow['pid'], '', 0, false);
                    } else {
                        $config['title'] .= sprintf(' | %s', BackendUtility::getRecordTitle('tt_content', $originalRow));
                    }
                    break;
                case EnumType::New:
                    $fakeContentRow = [
                        'CType' => $row['content_type'],
                    ];
                    $config['saveAndClose'] = $row['save_and_close'];
                    $config['iconIdentifier'] = $iconFactory->mapRecordTypeToIconIdentifier('tt_content', $fakeContentRow);
                    $config['title'] .= sprintf(' | %s', BackendUtility::getProcessedValue('tt_content', 'CType', $row['content_type']));
                    break;
            }

            $groupIdentifier = $row['direct'] ? EnumType::Pinned->value : $type->value;
            $identifier = 'favorite' . $groupIdentifier . '_' . $row['uid'];
            $items[$groupIdentifier][$identifier] = $config;
        }

        return $items;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

}
