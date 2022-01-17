<?php
namespace Mexbs\ApBase\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_popup_on_first_visit',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Popup On First Customer Visit'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'popup_on_first_visit_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Popup Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote'),
                'hint_messages',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Hint Messages'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_cart_hints',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Cart Hints'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'actions_hint_label',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '1K',
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Actions Label for Cart Hints'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'hide_hints_after_discount_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Hide Cart Hints after the Discount Number'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_cart_hints_if_coupon_invalid',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Cart Hints When Coupon is Invalid'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            /**
             * Create table 'apactionrule_product'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('apactionrule_product'))
                ->addColumn(
                    'apactionrule_product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'AP Action Rule Product Id'
                )
                ->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Rule Id'
                )
                ->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Product Id'
                )
                ->addColumn(
                    'group_number',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true, 'default' => null],
                    'Group Number'
                )
                ->addColumn(
                    'product_has_custom_options',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    ['default' => 0],
                    'Whether Product has custom options'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'apactionrule_product',
                        ['rule_id', 'product_id', 'group_number'],
                        true
                    ),
                    ['rule_id', 'product_id', 'group_number'],
                    ['type' => 'unique']
                )->addForeignKey(
                    $installer->getFkName('apactionrule_product', 'rule_id', 'salesrule', 'rule_id'),
                    'rule_id',
                    $installer->getTable('salesrule'),
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName('apactionrule_product', 'rule_id', 'catalog_product_entity', 'entity_id'),
                    'rule_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('AP Rule Product');

            $installer->getConnection()->createTable($table);

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_promo_block',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Promo Blocks'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'hide_promo_block_if_rule_applied',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Display Promo Blocks'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.5.4', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'hint_messages',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1M',
                    'default' => null,
                    'comment' => 'Hint Messages'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'gift_rule_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Gift Rule ID'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'gift_trigger_item_ids_qtys',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '10K',
                    'default' => null,
                    'comment' => 'Gift Trigger Item IDs and quantities'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'gift_message',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1M',
                    'default' => null,
                    'comment' => 'Gift Message'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'gift_qtys_can_add_per_group',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1K',
                    'default' => null,
                    'comment' => 'Gift Qtys one can add per group'
                ]
            );
            $setup->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'gift_trigger_item_ids_qtys_of_same_group',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1M',
                    'default' => null,
                    'comment' => 'Gift Trigger Item Ids  and quantities of the Same Group'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote'),
                'gift_hint_message',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1M',
                    'default' => null,
                    'comment' => 'Gift Hint Message'
                ]
            );
            $setup->getConnection()->addColumn(
                $installer->getTable('quote'),
                'gift_qtys_can_add_per_group',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1K',
                    'default' => null,
                    'comment' => 'Gift qtys can add per group per rule'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_product_hints',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Product Hints'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'product_hints_location',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '100',
                    'nullable' => false,
                    'default' => 'config',
                    'comment' => 'The Location of the Product Hints'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'enable_auto_add',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Enable Auto Add'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_banner_in_promo_trigger_products',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Banner in Promo Trigger Products'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'banner_in_promo_trigger_products_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Banner in Promo Trigger Products Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_badge_in_promo_trigger_products',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Badge in Promo Trigger Products'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'badge_in_promo_trigger_products_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Badge in Promo Trigger Products Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_banner_in_get_products',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Banner in Get Products'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'banner_in_get_products_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Banner in Get Products Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_badge_in_get_products',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Badge in Get Products'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'badge_in_get_products_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Badge in Get Products Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('apactionrule_product'),
                'rule_action_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Rule action type'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('apactionrule_product'),
                'group_action_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Group action type'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.5.10', '<')) {
            $setup->getConnection()->dropForeignKey($installer->getTable('apactionrule_product'), $installer->getFkName('apactionrule_product', 'rule_id', 'catalog_product_entity', 'entity_id'));
            $setup->getConnection()->addForeignKey(
                $installer->getFkName('apactionrule_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                $installer->getTable('apactionrule_product'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id'
            );
        }

        if (version_compare($context->getVersion(), '1.5.11', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_badge_in_promo_trigger_products_category',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Badge in Promo Trigger Products in the Category pages'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'badge_in_promo_trigger_products_category_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Badge in Promo Trigger Products Image in the Category pages'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_badge_in_get_products_category',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => '6',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Badge in Get Products in the Category pages'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'badge_in_get_products_category_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Badge in Get Products Image in the Category pages'
                ]
            );
        }

		if (version_compare($context->getVersion(), '1.5.10', '<')) {
            $setup->getConnection()->dropColumn(
                $installer->getTable('salesrule'),
                'display_promo_block'
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_promo_block_in_cart',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Promo Block in Cart'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'display_promo_block_in_product',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Display Promo Block in Product Page'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'promo_block_title_in_product',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '1K',
                    'default' => null,
                    'comment' => 'Promo Block Title in Product Page'
                ]
            );
        }

		if (version_compare($context->getVersion(), '1.6.15', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'message_to_display',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '1K',
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Message to Display in the Cart and the Checkout'
                ]
            );

            $setup->getConnection()->addColumn(
                $installer->getTable('quote'),
                'extra_messages_to_display',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Extra Messages'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.6.18', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'discount_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Discount method'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.6.21', '<')) {
            $setup->getConnection()->modifyColumn(
                $installer->getTable('salesrule'),
                'actions_hint_label',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '1K',
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Actions Label for Cart Hints'
                ]
            );
        }

        $installer->endSetup();
    }
}
