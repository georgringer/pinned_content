<?php

declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\EventListener;

use Doctrine\DBAL\ParameterType;
use GeorgRinger\FavoriteContent\Enum\EnumType;
use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsEventListener()]
final class ProvideFavoriteContentElementsListener
{
    public function __construct(
        private UriBuilder $uriBuilder,
    ) {}

    protected string $returnUrl = '';

    public function __invoke(ModifyNewContentElementWizardItemsEvent $event)
    {
        $items = $event->getWizardItems();
        $request = $GLOBALS['TYPO3_REQUEST'];
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $this->returnUrl = GeneralUtility::sanitizeLocalUrl($parsedBody['returnUrl'] ?? $queryParams['returnUrl'] ?? '');

        $groupedFavorites = $this->getGroupedFavorites($event);
        foreach ($groupedFavorites as $type => $groupedItems) {
            $enum = EnumType::tryFrom($type);
            if ($enum === null) {
                continue;
            }
            $items['favorite' . $enum->name] = [
                'header' => '❤️ Favorites  [' . $enum->name . ']',
            ];
            $items += $groupedItems;
        }
        $event->setWizardItems($items);
    }


    private function getGroupedFavorites(ModifyNewContentElementWizardItemsEvent $event): array
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_favorite_content_item');
        $rows = $queryBuilder
            ->select('*')
            ->from('tx_favorite_content_item')
            ->where(
                $queryBuilder->expr()->eq('cruser', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], ParameterType::INTEGER)),
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        if (!$rows) {
            return [];
        }
        $items = [];
        foreach ($rows as $row) {
            $fakeContentRow = [
                'CType' => $row['content_type'],
            ];
            $config = [
                'title' => $row['name'],
                'description' => $row['description'],
                'iconIdentifier' => $iconFactory->mapRecordTypeToIconIdentifier('tt_content', $fakeContentRow),
                'defaultValues' => [
                    'CType' => $row['content_type'],
                    '_favorite' => $row['uid'],
                ],
            ];
            switch ($row['type']) {
                case EnumType::Copy->value:
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
                    $config['title'] .= sprintf(' | %s', BackendUtility::getRecordTitle('tt_content', BackendUtility::getRecord('tt_content', $row['record'])));
                    break;
                case EnumType::New->value:
                    $config['saveAndClose'] = $row['save_and_close'];
                    $config['title'] .= sprintf(' | %s', BackendUtility::getProcessedValue('tt_content', 'CType', $row['content_type']));
                    break;
            }

            $identifier = 'favorite' . EnumType::Copy->name . '_' . $row['uid'];
            $items[$row['type']][$identifier] = $config;
        }

        return $items;
    }

}
