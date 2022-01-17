<?php

namespace Cminds\Marketplace\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory      $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Config               $resourceConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexerRegistry      $indexerRegistry
     * @param EavConfig            $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        Config $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        IndexerRegistry $indexerRegistry,
        EavConfig $eavConfig,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     * @throws \Exception
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'fee_type',
                [
                    'type' => 'text',
                    'label' => __('Sales Fee Type'),
                    'input' => 'select',
                    'source' => 'Cminds\Marketplace\Model\Config\Source\Fee\Type',
                    'default' => 1,
                    'visible' => true,
                    'admin_only' => true,
                    'system' => 0,
                ]
            );
            $customerSetup
                ->getEavConfig()
                ->getAttribute(
                    'customer',
                    'fee_type'
                )
                ->setData('used_in_forms', ['adminhtml_customer'])
                ->save();

            $eavSetup->addAttribute(
                Category::ENTITY,
                'marketplace_fee',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => Attribute::SCOPE_GLOBAL,
                    'input' => 'text',
                    'label' => __('Sales Fee'),
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                ]
            );

            $eavSetup->addAttribute(
                Category::ENTITY,
                'marketplace_fee_type',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => Attribute::SCOPE_GLOBAL,
                    'input' => 'select',
                    'label' => __('Sales Fee Type'),
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => 1,
                    'source' => 'Cminds\Marketplace\Model\Config\Source\Fee\Type',
                ]
            );

            $eavSetup->addAttribute(
                Product::ENTITY,
                'marketplace_fee',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => Attribute::SCOPE_GLOBAL,
                    'input' => 'text',
                    'label' => __('Sales Fee'),
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                ]
            );

            $eavSetup->addAttribute(
                Product::ENTITY,
                'marketplace_fee_type',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => Attribute::SCOPE_GLOBAL,
                    'input' => 'select',
                    'label' => __('Sales Fee Type'),
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => 1,
                    'source' => 'Cminds\Marketplace\Model\Config\Source\Fee\Type',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $carrierTitle = $this->scopeConfig->getValue('carriers/supplier/title');
            if ($carrierTitle !== 'Supplier') {
                $this->resourceConfig->saveConfig(
                    'carriers/supplier/title',
                    'Supplier',
                    'default',
                    0
                );
            }
        }

        if (version_compare($context->getVersion(), '1.3.6', '<')) {
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(
                Customer::ENTITY,
                'email_shipping_company',
                [
                    'type' => 'text',
                    'label' => __('Shipping company Email'),
                    'input' => 'text',
                    'required' => false,
                    'default' => '',
                    'visible' => true,
                    'admin_only' => false,
                    'system' => false,
                ]
            );

            $customerSetup
                ->getEavConfig()
                ->getAttribute(Customer::ENTITY, 'email_shipping_company')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer'],
                ])
                ->save();
        }

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();

        $this->eavConfig->clear();

        $setup->endSetup();
    }
}
