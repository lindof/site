<?php

namespace Orange35\Colorpickercustom\Plugin\CatalogSearch\Block\Advanced;

class Result
{
    private $eventManager;

    public function __construct(\Magento\Framework\Event\ManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Trigger event to call \Orange35\Colorpickercustom\Observer\ProductListCollection     *
     * @param \Magento\CatalogSearch\Block\Advanced\Result $subject
     * @param $result
     * @return mixed
     */
    public function afterSetListCollection(\Magento\CatalogSearch\Block\Advanced\Result $subject, $result)
    {
        /** @var \Magento\Catalog\Block\Product\ListProduct $block */
        $block = $subject->getChildBlock('search_result_list');
        $collection = $block->getLoadedProductCollection();
        $this->eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );
        return $result;
    }
}
