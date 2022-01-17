<?php

namespace Orange35\ImageConstructor\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'image',
                [
                    'type'     => Table::TYPE_TEXT,
                    'LENGTH'   => 255,
                    'nullable' => true,
                    'default'  => null,
                    'comment'  => 'Image',
                    'after'    => 'name',
                ]
            );
        }
        $setup->endSetup();
    }
}
