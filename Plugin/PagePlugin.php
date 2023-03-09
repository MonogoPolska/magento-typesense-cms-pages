<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Plugin;

use Magento\Cms\Model\ResourceModel\Page;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\AbstractModel;
use Monogo\TypesenseCmsPages\Services\ConfigService;

class PagePlugin
{
    /**
     * @var IndexerInterface
     */
    private IndexerInterface $indexer;

    /**
     * @var ConfigService
     */
    private ConfigService $configService;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param ConfigService $configService
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        ConfigService   $configService
    )
    {
        $this->indexer = $indexerRegistry->get('typesense_cms_pages');
        $this->configService = $configService;
    }

    /**
     * @param Page $pageResource
     * @param AbstractModel $page
     * @return AbstractModel[]
     */
    public function beforeSave(Page $pageResource, AbstractModel $page)
    {
        if (!$this->configService->isConfigurationValid()) {
            return [$page];
        }

        $pageResource->addCommitCallback(function () use ($page) {
            if (!$this->indexer->isScheduled()) {
                $this->indexer->reindexRow($page->getId());
            }
        });

        return [$page];
    }

    /**
     * @param Page $pageResource
     * @param AbstractModel $page
     * @return AbstractModel[]
     */
    public function beforeDelete(Page $pageResource, AbstractModel $page)
    {
        if (!$this->configService->isConfigurationValid()) {
            return [$page];
        }

        $pageResource->addCommitCallback(function () use ($page) {
            if (!$this->indexer->isScheduled()) {
                $this->indexer->reindexRow($page->getId());
            }
        });

        return [$page];
    }
}
