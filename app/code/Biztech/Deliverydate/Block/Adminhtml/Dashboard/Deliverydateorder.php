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
use Magento\Backend\Block\Dashboard\Grid as DashboardGrid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelperData;

class Deliverydateorder extends DashboardGrid
{
    protected $_template = 'Biztech_Deliverydate::dashboard/grid.phtml';

    /**
     * @var \Magento\Reports\Model\ResourceModel\Order\CollectionFactory
     */
    const XML_PATH_DTENABLE = 'deliverydate/deliverydate_general/deliverytime_enable_time';
    const XML_PATH_DD = 'deliverydate/deliverydate_general/deliverydate_format';
    const XML_PATH_DT = 'deliverydate/deliverydate_general/deliverytime_format';

    protected $_collectionFactory;
    protected $_helper;
    protected $_scopeConfig;


    /**
     * @param Context $context
     * @param BackendHelperData $backendHelper
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelperData $backendHelper,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
        //  $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('deliverydate_orders');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(false);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        $request = $this->_helper->getRequest();
        $new_date = date('Y-m-d', time());
        $dateStart = date('Y-m-d 00:00:00', strtotime($new_date));
        $dateEnd = date('Y-m-d 23:59:59', strtotime($new_date));

        $rDeliveryFrom = $request->getParam('delivery_from');
        $rDeliveryTo = $request->getParam('delivery_to');

        if (isset($rDeliveryFrom) && $rDeliveryFrom !== '') {
            $dateStart1 = $rDeliveryFrom;
            $dateStart = date('Y-m-d 00:00:00', strtotime($dateStart1));
        }

        if (isset($rDeliveryTo) && $rDeliveryTo !== '') {
            $dateEnd1 = $rDeliveryTo;
            $dateEnd = date('Y-m-d 00:00:00', strtotime($dateEnd1));
        }

        //get order collection
        $collection = $this->_helper->getOrderCollection($dateStart, $dateEnd);

        $this->setCollection($collection);
        $this->setData('orders', $collection);
        $this->setData('delivery_from', $dateStart);
        $this->setData('delivery_to', $dateEnd);

        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $cnt = 0;
        $date_format = $this->_scopeConfig->getValue(self::XML_PATH_DD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $time_format = $this->_scopeConfig->getValue(self::XML_PATH_DT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($date_format === '') {
            $date_format = 'd/M/Y';
        }
        if ($time_format === '') {
            $date_format .= ',g:i a';
        } else {
            $is_time = $this->_scopeConfig->getValue(self::XML_PATH_DTENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($is_time) {
                $date_format .= ' ' . $time_format;
            } else {
                $date_format .= ' ';
            }
        }

        $this->addColumn('increment_id', ['header' => __('Order#'), 'index' => 'increment_id']);
        $this->addColumn(
            'created_at',
            ['header' => __('Purchased on'), 'index' => 'created_at', 'type' => 'datetime']
        );

        $this->addColumn('billing-name', [
            'header' => __('Bill-to Name'),
            'index' => ['billing_firstname', 'billing_lastname'],
            'type' => 'concat',
            'separator' => ' ',
        ]);
        $this->addColumn('shipping-name', [
            'header' => __('Ship-to Name'),
            'index' => ['shipping_firstname', 'shipping_lastname'],
            'type' => 'concat',
            'separator' => ' ',
        ]);
        $this->addColumn('same_day_charges', ['header' => __('Same day charges'), 'index' => 'same_day_charges']);
        $this->addColumn('delivery_charges', ['header' => __('Specific timeslot charges'), 'index' => 'delivery_charges']);
        $this->addColumn('grand_total', ['header' => __('G.T. (Purchased)'), 'index' => 'grand_total']);
        $this->addColumn('base_grand_total', ['header' => __('G.T. (Base)'), 'index' => 'base_grand_total']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status']);
        $this->addColumn('shipping_arrival_date', [
            'header' => __('Delivery date'),
            'type' => 'text',
            'format' => $date_format,
            'index' => 'shipping_arrival_date',
        ]);
        $this->addExportType('deliverydate/deliverydate/exportdeliverydateorders', __('CSV'));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}
