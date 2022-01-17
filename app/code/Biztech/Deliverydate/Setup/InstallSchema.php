<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Biztech\Deliverydate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /* insert columns in quote table */
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'shipping_arrival_date',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'nullable' => true,
            'comment' => 'Shipping arrival date for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'shipping_arrival_comments',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival comments for Delivery Date Scheduler',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'shipping_arrival_slot',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival slots for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Delivery Charges for Delivery Date Scheduler',
            ]
        );

        /* insert columns in sales_order table */

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'shipping_arrival_date',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'nullable' => true,
            'comment' => 'Shipping arrival date for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'shipping_arrival_comments',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival comments for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'shipping_arrival_slot',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival slots for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Shipping arrival slots of Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Shipping arrival slots of Delivery Date Scheduler',
            ]
        );

        /* insert columns in quote_address table */
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'shipping_arrival_date',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'nullable' => true,
            'comment' => 'Shipping arrival date of Delivery Date Scheduler',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'shipping_arrival_comments',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival comments of Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'shipping_arrival_slot',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival slots of Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Shipping arrival slots of Delivery Date Scheduler',
            ]
        );

        /* insert columns in sales_order_grid table */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'shipping_arrival_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable' => false,
                'comment' => 'Delivery Date',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'shipping_arrival_comments',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Delivery comment',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'shipping_arrival_slot',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Delivery Slot',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'shipping_arrival_date',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'nullable' => true,
            'comment' => 'Shipping arrival date for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'shipping_arrival_comments',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival comments for Delivery Date Scheduler',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'shipping_arrival_slot',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival slots for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Delivery Charges for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'shipping_arrival_date',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'nullable' => true,
            'comment' => 'Shipping arrival date for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'shipping_arrival_comments',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival comments for Delivery Date Scheduler',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'shipping_arrival_slot',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Shipping arrival slots for Delivery Date Scheduler',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'delivery_charges',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'comment' => 'Delivery Charges for Delivery Date Scheduler',
            ]
        );

        $setup->endSetup();
    }
}
