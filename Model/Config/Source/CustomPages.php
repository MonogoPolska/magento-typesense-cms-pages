<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Page\Collection;
use Magento\Framework\App\ObjectManager;
use Monogo\TypesenseCore\Model\Config\Source\AbstractTable;

class CustomPages extends AbstractTable
{
    /**
     * @var Collection|null
     */
    protected ?Collection $collection = null;

    /**
     * @return array[]
     */
    protected function getTableData(): array
    {
        return [
            'attribute' => [
                'label' => 'Page',
                'values' => function () {
                    $options = [];
                    $magentoPages = $this->getCollection()->addFieldToFilter('is_active', 1);

                    foreach ($magentoPages as $page) {
                        $options[$page->getData('identifier')] = $page->getData('identifier');
                    }

                    return $options;
                },
            ],
        ];
    }

    /**
     * @return Collection
     */
    protected function getCollection(): Collection
    {
        if (!$this->collection instanceof Collection) {
            $objectManager = ObjectManager::getInstance();
            $this->collection = $objectManager->create(Collection::class);
        }
        return $this->collection;
    }
}
