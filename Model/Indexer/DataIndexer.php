<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Model\Indexer;

use Magento\Framework\Message\ManagerInterface;
use Monogo\TypesenseCore\Model\Indexer\DataIndexer as DataIndexerCore;
use Monogo\TypesenseCore\Model\Queue;
use Monogo\TypesenseCmsPages\Model\Entity\DataProvider;
use Monogo\TypesenseCmsPages\Services\ConfigService;
use Symfony\Component\Console\Output\ConsoleOutput;

class DataIndexer extends DataIndexerCore
{
    /**
     * @param DataProvider $dataProvider
     * @param IndexerRunner $indexerRunner
     * @param Queue $queue
     * @param ConfigService $configService
     * @param ManagerInterface $messageManager
     * @param ConsoleOutput $output
     */
    public function __construct(
        DataProvider     $dataProvider,
        IndexerRunner    $indexerRunner,
        Queue            $queue,
        ConfigService    $configService,
        ManagerInterface $messageManager,
        ConsoleOutput    $output
    )
    {
        parent::__construct(
            $dataProvider,
            $indexerRunner,
            $queue,
            $configService,
            $messageManager,
            $output
        );
    }
}
