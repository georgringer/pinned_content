<?php
declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\Repository;

use Doctrine\DBAL\ParameterType;
use GeorgRinger\FavoriteContent\Enum\EnumType;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class FavoriteRepository
{

    private const TABLE = 'tx_favorite_content_item';

    public function getAllOfCurrentUser(): array
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        return $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('cruser', $queryBuilder->createNamedParameter($this->getBackendUserId(), ParameterType::INTEGER)),
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getByUid(int $id): ?array
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('direct', $queryBuilder->createNamedParameter(1, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter(EnumType::Copy->value, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('cruser', $queryBuilder->createNamedParameter($this->getBackendUserId(), ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('record', $queryBuilder->createNamedParameter($id, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($row) || empty($row)) {
            return null;
        }
        return $row;
    }

    public function add(int $id)
    {
        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);

        $record = BackendUtility::getRecord('tt_content', $id);
        if (!$record) {
            throw new \RuntimeException('Record not found');
        }

        $cmd = [];
        $data[self::TABLE][StringUtility::getUniqueId('NEW')] = [
            'pid' => $record['pid'],
            'direct' => 1,
            'record' => $id,
            'type' => EnumType::Copy->value,
        ];
        $dataHandler->start($data, $cmd);
        $dataHandler->process_datamap();
    }

    public function remove(int $id): void
    {
        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $cmd[self::TABLE][$id]['delete'] = 1;
        $dataHandler->start([], $cmd);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();
    }

    private function getBackendUserId(): int
    {
        return $GLOBALS['BE_USER']->user['uid'];
    }

    private function getConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::TABLE);
    }

}
