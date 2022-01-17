<?php

namespace Orange35\ImageConstructor\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('catalog_product_option_type_value'),
            'layer',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Layer',
            ]
        );

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

        $setup->endSetup();
    }
}
