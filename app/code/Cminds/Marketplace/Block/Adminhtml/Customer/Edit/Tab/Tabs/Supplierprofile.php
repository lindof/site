<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Tabs;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Supplierprofile
 * @package Cminds\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Tabs
 */
class Supplierprofile extends Container
{
    /**
     * Pseudo construct
     */
    public function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customer_edit_tab_tabs_supplierprofile';
        $this->_blockGroup = 'Cminds_Marketplace';

        $this->removeButton('save');
        $this->removeButton('delete');
        $this->removeButton('back');
        $this->removeButton('reset');
    }

    /**
     * @return string
     */
    public function getHeaderHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-cms-page';
    }
}
