<?php
namespace Biztech\Deliverydate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            /* insert columns in quote_address table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote_address'),
                'same_day_charges',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Same Day Delivery Charges of Delivery Date Scheduler'
                ]
            );

            $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote_address'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );
       
            /* insert columns in quote table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote'),
                'same_day_charges',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Same Day Delivery Charges of Delivery Date Scheduler'
                ]
            );

            $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );

        
            /* insert columns in sales_order table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_order'),
                'same_day_charges',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Same Day Delivery Charges of Delivery Date Scheduler'
                ]
            );
        
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_order'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );

            /* insert columns in sales_invoice table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_invoice'),
                'same_day_charges',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Same Day Delivery Charges of Delivery Date Scheduler'
                ]
            );

            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_invoice'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );

            /* insert columns in sales_creditmemo table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'same_day_charges',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Same Day Delivery Charges of Delivery Date Scheduler'
                ]
            );


            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );

            /* insert columns in sales_order_grid table */
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_order_grid'),
                'call_before_delivery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'default' => 0,
                    'nullable' => true,
                    'default' => '0',
                    'comment' =>'Call Before Delivery of Delivery Date Scheduler'
                ]
            );
        }
        $setup->endSetup();
    }
}
