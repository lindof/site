<?php

namespace Cminds\Supplierfrontendproductuploader\Model\Config\Source\Attribute;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as CatalogAttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as CatalogAttributeCollectionFactory;

class AvailableSupplier implements OptionSourceInterface
{

    /**
     * @var CatalogAttributeCollectionFactory
     */
    protected $catalogAttributeCollectionFactory;

    /**
     * AvailableSupplier constructor.
     * @param CatalogAttributeCollectionFactory $catalogAttributeCollectionFactory
     */
    public function __construct(CatalogAttributeCollectionFactory $catalogAttributeCollectionFactory)
    {
        $this->catalogAttributeCollectionFactory = $catalogAttributeCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var CatalogAttributeCollection $catalogAttributeCollection */
        $catalogAttributeCollection = $this->catalogAttributeCollectionFactory->create();

        $catalogAttributeCollection->addFieldToFilter('available_for_supplier', 1)
            ->addFieldToFilter('frontend_input', 'select');

        $options[] = [
            'label' => __('Please Select'),
            'value' => ''
        ];

        foreach ($catalogAttributeCollection as $catalogAttributeItem) {
            $options[] = [
                'label' => $catalogAttributeItem->getDefaultFrontendLabel(),
                'value' => $catalogAttributeItem->getAttributeCode(),
            ];
        }

        return $options;
    }
}
