<?php 
namespace Knowband\Spinandwin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        //Check if the table already exists
        if ($installer->getConnection()->isTableExists('vss_spinandwin_email') != true) {
            /**
             * Create table 'vss_spinandwin_email'
             */
            $emailTable = $installer->getConnection()
                ->newTable($installer->getTable('vss_spinandwin_email'))
                ->addColumn(
                    'template_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, 
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Email Template ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    ['unsigned' => true, 'nullable' => true, 'default' => '0'],
                    'Magento Store ID'
                )
                ->addColumn(
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    ['unsigned' => true, 'nullable' => true],
                    'Magento Website ID'
                )
                ->addColumn(
                    'template_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Email Template Name'
                )
                ->addColumn(
                    'template_subject',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1000,
                    ['nullable' => false],
                    'Email Template Subject'
                )
                ->addColumn(
                    'template_content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                    ['nullable' => false],
                    'Email Template HTML Content'
                )
                ->addColumn(
                    'template_description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                    ['nullable' => true],
                    'Email Template Description'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Email Template Creation Date'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Email Template Last Updation Date'
                )
                ->addIndex($installer->getIdxName('vss_spinandwin_email', ['store_id']), ['store_id'])
                ->addIndex($installer->getIdxName('vss_spinandwin_email', ['website_id']), ['website_id'])
                ->setComment('TABLE FOR SAVING DATA OF SPIN AND WIN USERS');
            $installer->getConnection()->createTable($emailTable);
        }
        
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists('vss_spinandwin_user_list') != true) {
            /**
             * Create table 'vss_spinandwin_user_list'
             */
            $userTable = $installer->getConnection()
                ->newTable($installer->getTable('vss_spinandwin_user_list'))
                ->addColumn(
                    'id_user_list',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, 
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'User List ID'
                )
                ->addColumn(
                    'coupon_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, 
                    ['unsigned' => true, 'nullable' => false],
                    'Coupon ID'
                )
                ->addColumn(
                    'customer_email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer EMail ID'
                )
                ->addColumn(
                    'coupon_usage',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false],
                    'Coupon Usage'
                )
                ->addColumn(
                    'country',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'User Country'
                )
                ->addColumn(
                    'device',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'User Device'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'User List Creation Date'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'User List Last Updation Date'
                )
                ->addIndex($installer->getIdxName('vss_spinandwin_user_list', ['coupon_id']), ['coupon_id'])
                ->setComment('TABLE FOR SAVING DATA OF SPIN AND WIN EMAIL TEMPLATES');
            $installer->getConnection()->createTable($userTable);
        }
        
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists('vss_spinandwin_coupons') != true) {
            /**
             * Create table 'vss_spinandwin_coupons'
             */
            $couponTable = $installer->getConnection()
                ->newTable($installer->getTable('vss_spinandwin_coupons'))
                ->addColumn(
                    'coupon_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, 
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Spin Win Coupon ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    ['unsigned' => true, 'nullable' => true, 'default' => '0'],
                    'Magento Store ID'
                )
                ->addColumn(
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    ['unsigned' => true, 'nullable' => true],
                    'Magento Website ID'
                )
                ->addColumn(
                    'coupon_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, 
                    ['nullable' => false],
                    'Coupon Code'
                )
                ->addColumn(
                    'coupon_value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50, 
                    ['nullable' => false],
                    'Coupon Value'
                )
                ->addColumn(
                    'coupon_value_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20, 
                    ['nullable' => false],
                    'Coupon Value Type'
                )
                ->addColumn(
                    'use_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1, 
                    ['nullable' => true],
                    'Coupon Use Type'
                )
                ->addColumn(
                    'discount_currency',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    11,
                    ['nullable' => false],
                    'Coupon Discount Currency'
                )
                ->addColumn(
                    'coupon_expire_in_days',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    2,
                    ['nullable' => false],
                    'Coupon Expiry (in Days)'
                )
                ->addColumn(
                    'coupon_expire_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Coupon Expiry Date'
                )                
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'User List Creation Date'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'User List Last Updation Date'
                )
                ->addIndex($installer->getIdxName('vss_spinandwin_coupons', ['store_id']), ['store_id'])
                ->addIndex($installer->getIdxName('vss_spinandwin_coupons', ['website_id']), ['website_id'])
                ->setComment('TABLE FOR SAVING DATA OF SPIN AND WIN COUPONS');
            $installer->getConnection()->createTable($couponTable);
        }
    }
}