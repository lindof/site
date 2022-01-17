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

namespace Biztech\Deliverydate\Block\Adminhtml\Dashboard;

use Biztech\Deliverydate\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;

/**
 * Adminhtml dashboard bottom tabs.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grids extends Tabs
{

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';
    protected $_helper;

    /**
     * @param Context $context
     * @param EncoderInterface $encoderInterface
     * @param Session $authSession
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoderInterface,
        Session $authSession,
        Data $helperData
    ) {
        $this->_helper = $helperData;
        parent::__construct($context, $encoderInterface, $authSession);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        // $this->_helper = $helper;
        $this->setId('grid_tab');
        $this->setDestElementId('grid_tab_content');
    }

    /**
     * Prepare layout for dashboard bottom tabs.
     *
     * To load block statically:
     *     1) content must be generated
     *     2) url should not be specified
     *     3) class should not be 'ajax'
     * To load with ajax:
     *     1) do not load content
     *     2) specify url (BE CAREFUL)
     *     3) specify class 'ajax'
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        // load this active tab statically
        $this->addTab(
            'ordered_products',
            [
                'label' => __('Bestsellers'),
                'content' => $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Dashboard\Tab\Products\Ordered'
                )->toHtml(),
                'active' => true,
            ]
        );

        // load other tabs with ajax
        $this->addTab(
            'reviewed_products',
            [
                'label' => __('Most Viewed Products'),
                'url' => $this->getUrl('adminhtml/*/productsViewed', ['_current' => true]),
                'class' => 'ajax',
            ]
        );

        $this->addTab(
            'new_customers',
            [
                'label' => __('New Customers'),
                'url' => $this->getUrl('adminhtml/*/customersNewest', ['_current' => true]),
                'class' => 'ajax',
            ]
        );

        $this->addTab(
            'customers',
            [
                'label' => __('Customers'),
                'url' => $this->getUrl('adminhtml/*/customersMost', ['_current' => true]),
                'class' => 'ajax',
            ]
        );


        if ($this->_helper->isEnable()) {
            $this->addTab('deliverydate_orders', [
                'label' => __('Today\'s Delivery Date Orders'),
                'url' => $this->getUrl('deliverydate/deliverydate/deliverydateorder', ['_current' => true]),
                'class' => 'ajax',
            ]);
        }

        if ($this->_helper->isEnable()) {
            $this->addTab('deliverydae_calender', [
                'label' => __('Delivery Date Calendar'),
                'url' => $this->getUrl('deliverydate/deliverydate/DeliverydateCalender', ['_current' => true]),
                'class' => 'ajax',
            ]);
        }


        return parent::_prepareLayout();
    }
}
