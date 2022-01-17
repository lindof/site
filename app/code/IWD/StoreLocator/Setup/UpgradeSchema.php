<?php

namespace IWD\StoreLocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion()
            && version_compare($context->getVersion(), '2.0.0.8') < 0
        ) {
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('storelocator_item_store'),
                    'item_id',
                    $setup->getTable('storelocator_item'),
                    'item_id'
                ),
                $setup->getTable('storelocator_item_store'),
                'item_id',
                $setup->getTable('storelocator_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }

        $setup->endSetup();
    }
}
