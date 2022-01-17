<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Bss\DependentCustomOption\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        if (!$installer->tableExists('bss_depend_co')) {
            $table = $connection
                ->newTable(
                    $installer->getTable('bss_depend_co')
                )
                ->addColumn(
                    'dependent_id',
                    Table::TYPE_SMALLINT,
                    6,
                    ['primary' => true, 'nullable' => false, 'auto_increment' => true],
                    'Option Type Image ID'
                )
                ->addColumn(
                    'depend_value',
                    Table::TYPE_TEXT,
                    255,
                    ['default' => ''],
                    'Url'
                )
                ->addColumn(
                    'option_type_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true],
                    'Url'
                )
                ->addColumn(
                    'option_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true],
                    'Url'
                )
                ->addIndex(
                    $installer->getIdxName('bss_depend_co', ['dependent_id']),
                    ['dependent_id']
                )
                ->setComment(
                    'Catalog Product Option Type Dependent'
                );
            $connection->createTable($table);
            $connection->addForeignKey(
                $connection->getForeignKeyName(
                    $setup->getTable('bss_depend_co'),
                    'option_type_id',
                    $setup->getTable('catalog_product_option_type_value'),
                    'option_type_id'
                ),
                $setup->getTable('bss_depend_co'),
                'option_type_id',
                $setup->getTable('catalog_product_option_type_value'),
                'option_type_id',
                Table::ACTION_SET_NULL
            );
            $connection->addForeignKey(
                $connection->getForeignKeyName(
                    $setup->getTable('bss_depend_co'),
                    'option_id',
                    $setup->getTable('catalog_product_option'),
                    'option_id'
                ),
                $setup->getTable('bss_depend_co'),
                'option_id',
                $setup->getTable('catalog_product_option'),
                'option_id',
                Table::ACTION_SET_NULL
            );
        }
        $defaultProductOptionTable = $setup->getTable('catalog_product_option');
        if ($connection->isTableExists($defaultProductOptionTable) == true) {
            $connection->addColumn(
                $defaultProductOptionTable,
                'bss_dco_require',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => '1',
                    'comment' => 'Option Qty'
                ]
            );
        }
        $installer->endSetup();
    }
}
