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
namespace Bss\DependentCustomOption\Model\ResourceModel;

use Bss\DependentCustomOption\Helper\ModuleConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Db_Statement_Interface;

/**
 * Class RemoveRequired
 *
 * @package Bss\DependentCustomOption\Model\ResourceModel
 */
class RemoveRequired
{
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var ModuleConfig
     */
    protected $helper;

    /**
     * RemoveRequired constructor.
     *
     * @param ResourceConnection $resource
     * @param ModuleConfig $helper
     */
    public function __construct(
        ResourceConnection $resource,
        ModuleConfig $helper
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
    }

    /**
     * Set and Get Value
     *
     * @return Zend_Db_Statement_Interface
     */
    public function getValue()
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('catalog_product_option');
        $update = 'UPDATE ' . $table . ' SET bss_dco_require = is_require, is_require = 0';
        return $connection->query($update);
    }
}
