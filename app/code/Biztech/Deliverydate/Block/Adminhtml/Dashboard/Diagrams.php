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
use Magento\Backend\Block\Dashboard\Diagrams as DashboardDiagrams;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;

/**
 * Adminhtml dashboard diagram tabs.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Diagrams extends DashboardDiagrams
{

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';
    protected $_bizHelper;

    /**
     * @param Context $Context
     * @param EncoderInterface $jsonEncoder
     * @param Session $backendSession
     * @param Data $bizHelper
     */
    public function __construct(
        Context $Context,
        EncoderInterface $jsonEncoder,
        Session $backendSession,
        Data $bizHelper
    ) {
        parent::__construct($Context, $jsonEncoder, $backendSession);
        $this->_bizHelper = $bizHelper;
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'orders',
            [
                'label' => __('Orders'),
                'content' => $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Tab\Orders')->toHtml(),
                'active' => true,
            ]
        );

        $this->addTab(
            'amounts',
            [
                'label' => __('Amounts'),
                'content' => $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Tab\Amounts')->toHtml(),
            ]
        );
        if ($this->_bizHelper->isEnable()) {
            $this->addTab('deliverydatecalendar', [
                'label' => __('DeliveryDate Calendar'),
                'url' => $this->getUrl('deliverydate/deliverydate/deliverydateCalender', ['_current' => true]),
                'class' => 'ajax',
            ]);
        }

        return parent::_prepareLayout();
    }
}
