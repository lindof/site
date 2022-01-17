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
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Cminds\Marketplace\Model\CategoriesFactory as CategoriesFactory;
use Cminds\Marketplace\Model\ResourceModel\Categories as CategoriesResource;

/**
 * Class SetNewCategoryRestrictedForSuppliers
 * @package Cminds\Marketplace\Observer
 */
class SetNewCategoryRestrictedForSuppliers implements ObserverInterface
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
    protected $customerCollectionFactory;

    /**
     * @var CategoriesFactory
     */
    protected $categoriesFactory;

    /**
     * @var CategoriesResource
     */
    protected $categoriesResource;

    /**
     * SetNewCategoryRestrictedForSuppliers constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param SupplierHelper $supplierHelper
     * @param CollectionFactory $customerCollectionFactory
     * @param CategoriesFactory $categoriesFactory
     * @param CategoriesResource $categoriesResource
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SupplierHelper $supplierHelper,
        CollectionFactory $customerCollectionFactory,
        CategoriesFactory $categoriesFactory,
        CategoriesResource $categoriesResource

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->supplierHelper = $supplierHelper;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->categoriesFactory = $categoriesFactory;
        $this->categoriesResource = $categoriesResource;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        if (!$this->supplierHelper->isEnabled()) {
            return $this;
        }

        if (!$this->isNewCategoriesAreUncheckedByDefault()) {
            return $this;
        }

        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');
        if ($category->getParentId() == Category::TREE_ROOT_ID) {
            return;
        }

        $suppliers = $this->customerCollectionFactory->create()
            ->addFieldToFilter(
                'group_id',
                [
                    'in' => [
                        $this->scopeConfig->getValue(
                            'configuration/suppliers_group/'
                            . 'supplier_group'
                        ),
                        $this->scopeConfig->getValue(
                            'configuration/suppliers_group/'
                            . 'suppliert_group_which_can_edit_own_products'
                        ),
                    ],
                ]
            );

        foreach($suppliers as $supplier) {
            $restrictedCategory = $this->categoriesFactory->create();
            $restrictedCategory->setSupplierId($supplier->getId());
            $restrictedCategory->setCategoryId($category->getId());
            $this->categoriesResource->save($restrictedCategory);
        }
    }

    /**
     * @return bool
     */
    private function isNewCategoriesAreUncheckedByDefault()
    {
        return (bool)$this->scopeConfig->getValue('configuration_marketplace/presentation/new_categories_are_unchecked_by_default');
    }
}
