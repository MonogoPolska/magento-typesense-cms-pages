<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Model\Entity;

use Exception;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;
use Monogo\TypesenseCore\Model\Entity\DataProvider as DataProviderCore;
use Monogo\TypesenseCmsPages\Adapter\IndexManager;
use Monogo\TypesenseCmsPages\Services\ConfigService;

class DataProvider extends DataProviderCore
{
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $eventManager;

    /**
     * @var PageCollectionFactory
     */
    private PageCollectionFactory $pageCollectionFactory;

    /**
     * @var ConfigService
     */
    private ConfigService $configService;

    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    /**
     * @var UrlFactory
     */
    private UrlFactory $frontendUrlFactory;

    /**
     * @var IndexManager
     */
    private IndexManager $indexManager;

    /**
     * @param ManagerInterface $eventManager
     * @param PageCollectionFactory $pageCollectionFactory
     * @param ConfigService $configService
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param UrlFactory $frontendUrlFactory
     * @param IndexManager $indexManager
     */
    public function __construct(
        ManagerInterface      $eventManager,
        PageCollectionFactory $pageCollectionFactory,
        ConfigService         $configService,
        FilterProvider        $filterProvider,
        StoreManagerInterface $storeManager,
        UrlFactory            $frontendUrlFactory,
        IndexManager          $indexManager
    )
    {
        parent::__construct($configService, $storeManager);
        $this->eventManager = $eventManager;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->configService = $configService;
        $this->filterProvider = $filterProvider;
        $this->frontendUrlFactory = $frontendUrlFactory;
        $this->indexManager = $indexManager;
    }

    /**
     * @return string
     */
    public function getIndexNameSuffix(): string
    {
        return '_cms_pages';
    }

    /**
     * @param int|null $storeId
     * @param array|null $dataIds
     * @return array
     * @throws Exception
     */
    public function getData(?int $storeId, array $dataIds = null): array
    {
        $magentoPages = $this->pageCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);

        if ($dataIds && count($dataIds)) {
            $magentoPages->addFieldToFilter('page_id', ['in' => $dataIds]);
        }

        $excludedPages = $this->getExcludedPageIds();
        if (count($excludedPages)) {
            $magentoPages->addFieldToFilter('identifier', ['nin' => $excludedPages]);
        }

        $this->eventManager->dispatch(
            'typesense_after_create_page_collection',
            ['collection' => $magentoPages]
        );

        $dataIdsToRemove = $dataIds ? array_flip($dataIds) : [];

        $pages = [];

        $frontendUrlBuilder = $this->frontendUrlFactory->create()->setScope($storeId);

        $indexSchema = $this->indexManager->getIndexFields();
        /** @var Page $page */
        foreach ($magentoPages as $page) {
            $pageObject = [];
            $page->setData('store_id', $storeId);

            if (!$page->getId()) {
                continue;
            }
            $pageObject['id'] = $page->getId();
            foreach ($indexSchema as $field) {
                $pageObject[$field['name']] = $page->getData($field['name']);
            }

            if (!empty($pageObject['content'])) {
                $content = $pageObject['content'];
                $content = $this->filterProvider->getPageFilter()->filter($content);
                $pageObject['content'] = $content;
                $pageObject['content_stripped'] = $this->strip($content, ['script', 'style']);
            }

            $pageObject['url'] = $frontendUrlBuilder->getUrl(
                null,
                [
                    '_direct' => $page->getIdentifier(),
                    '_secure' => true,
                ]
            );

            $transport = new DataObject($pageObject);
            $this->eventManager->dispatch(
                'typesense_after_create_page_object',
                ['page' => $transport, 'pageObject' => $page]
            );
            $pageObject = $transport->getData();

            if (isset($dataIdsToRemove[$page->getId()])) {
                unset($dataIdsToRemove[$page->getId()]);
            }
            $pages['toIndex'][] = $pageObject;
        }
        $pages['toRemove'] = array_unique(array_keys($dataIdsToRemove));
        return $pages;
    }

    /**
     * @return array
     */
    public function getExcludedPageIds(): array
    {
        $excludedPages = array_values($this->configService->getExcludedPages());
        foreach ($excludedPages as &$excludedPage) {
            $excludedPage = $excludedPage['attribute'];
        }

        return $excludedPages;
    }
}
