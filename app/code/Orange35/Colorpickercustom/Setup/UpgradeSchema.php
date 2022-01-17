<?php

namespace Orange35\Colorpickercustom\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $adapter = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $adapter->addColumn(
                $setup->getTable('catalog_product_option'),
                'tooltip',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default'  => 2,
                    'comment'  => 'Tooltip type',
                ]
            );
        }
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $adapter->addColumn(
                $setup->getTable('catalog_product_option'),
                'show_in_list',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default'  => 0,
                    'comment'  => 'Show in list',
                    'after'    => 'is_colorpicker',
                ]
            );
            $adapter->changeColumn(
                $setup->getTable('catalog_product_option'),
                'swatch_height',
                'swatch_height',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default'  => null,
                    'comment'  => 'Swatch height',
                ]
            );
            $adapter->changeColumn(
                $setup->getTable('catalog_product_option'),
                'swatch_width',
                'swatch_width',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default'  => null,
                    'comment'  => 'Swatch width',
                ]
            );
            $connection = $setup->getConnection();

            $connection->query(
                'UPDATE ' . $setup->getTable('catalog_product_option') . ' '
                . 'SET swatch_width = NULL '
                . 'WHERE swatch_width = 0'
            );
            $connection->query(
                'UPDATE ' . $setup->getTable('catalog_product_option') . ' '
                . 'SET swatch_height = NULL '
                . 'WHERE swatch_height = 0'
            );
        }

        $setup->endSetup();
    }
}
