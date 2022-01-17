<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Observer;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Cminds\Marketplace\Model\ResourceModel\Categories\CollectionFactory as CollectionFactory;

/**
 * Class DeleteRestrictedCategoryFromSuppliers
 * @package Cminds\Marketplace\Observer
 */
class DeleteRestrictedCategoryFromSuppliers implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SupplierHelper
     */
    protected $supplierHelper;

    /**
     * @var CollectionFactory
     */
    protected $categoriesCollection;

    /**
     * DeleteRestrictedCategoryFromSuppliers constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param SupplierHelper $supplierHelper
     * @param CollectionFactory $categoriesCollection
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SupplierHelper $supplierHelper,
        CollectionFactory $categoriesCollection

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->supplierHelper = $supplierHelper;
        $this->categoriesCollection = $categoriesCollection;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->supplierHelper->isEnabled()) {
            return $this;
        }

        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');
        if ($category->getParentId() == Category::TREE_ROOT_ID) {
            return;
        }

        $categories = $this->categoriesCollection->create();
        $categories->addFieldToFilter('category_id', $category->getId());

        foreach ($categories as $categorie) {
            $categorie->delete();
        }
    }
}
