<?php

namespace Orange35\ImageConstructor\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $adapter = $installer->getConnection();
        $value = $installer->getTable('catalog_product_option_type_value');
        $adapter->dropColumn($value, 'layer');
        if (version_compare($context->getVersion(), '1.2', '<=')) {
            $adapter->dropColumn($installer->getTable('sales_order_item'), 'image');
        }

        $installer->endSetup();
    }
}
