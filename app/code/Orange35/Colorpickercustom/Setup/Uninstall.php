<?php

namespace Orange35\Colorpickercustom\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $adapter = $setup->getConnection();

        $option = $setup->getTable('catalog_product_option');
        $adapter->dropColumn($option, 'is_colorpicker');
        $adapter->dropColumn($option, 'show_in_list');
        $adapter->dropColumn($option, 'swatch_height');
        $adapter->dropColumn($option, 'swatch_width');
        $adapter->dropColumn($option, 'tooltip');

        $value = $setup->getTable('catalog_product_option_type_value');
        $adapter->dropColumn($value, 'image');
        $adapter->dropColumn($value, 'color');
        $adapter->dropColumn($value, 'swatch_width');
        $adapter->dropColumn($value, 'swatch_height');

        $setup->endSetup();
    }
}
