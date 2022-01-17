<?php

namespace Knowband\Spinandwin\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            //Check if the table already exists

            $table_name = $installer->getTable('vss_spin_user_data');
            if ($installer->getConnection()->isTableExists($table_name) != true) {

                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `fname` varchar(30) DEFAULT NULL,
                `lname` varchar(30) DEFAULT NULL,
                `email` varchar(50) NOT NULL,
                `date_added` datetime DEFAULT CURRENT_TIMESTAMP,                 
                PRIMARY KEY(id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);
            }
            
            
        }
        
        if (version_compare($context->getVersion(), '3.0.0', '<')) {
            $table_name = $installer->getTable('vss_spin_theme_scheduling');
            if ($installer->getConnection()->isTableExists($table_name) != true) {

                $table_script = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(2) NOT NULL,
                `from_date` datetime DEFAULT NULL,
                `to_date` datetime NOT NULL,
                `settings` text NOT NULL,
                `date_added` datetime DEFAULT CURRENT_TIMESTAMP,                 
                PRIMARY KEY(schedule_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                $installer->run($table_script);
            }
        }

        $installer->endSetup();
    }

}
