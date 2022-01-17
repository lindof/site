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

namespace Biztech\Deliverydate\Controller\Adminhtml\Deliverydate;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\Dashboard;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class Getchartdata extends Dashboard
{
    //const DD_FORMAT = 'deliverydate/deliverydate_general/deliverydate_format';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    // protected $_scopeConfig;


    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $delivery_from = $this->getRequest()->getParam('delivery_from');
        $delivery_to = $this->getRequest()->getParam('delivery_to');
        $new_date = date('Y-m-d', time());
        $dateStart = date('Y-m-d 00:00:00', strtotime($new_date));
        $dateEnd = date('Y-m-d 23:59:59', strtotime($new_date));
        if (isset($delivery_from) && $delivery_from !== '') {
            $dateStart = $delivery_from;
        }
        if (isset($delivery_to) && $delivery_to !== '') {
            $dateEnd = $delivery_to;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollectionByDate = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Collection')
            ->addAttributeToSelect('shipping_arrival_date')
            ->addAttributeToFilter('shipping_arrival_date', ['from' => $dateStart, 'to' => $dateEnd]);
        $orderCollectionByDate->getSelect()->columns('COUNT(*) as total_orders');
        $orderCollectionByDate->getSelect()->group('date(shipping_arrival_date)');
        $config = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        //$date_format =$this->_scopeConfig->getValue(self::DD_FORMAT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $date_format = $config->getValue('deliverydate/deliverydate_general/deliverydate_format');

        if ($date_format === '') {
            $date_format = 'd/M/Y';
        }

        $data[0] = ['Dates', 'Number of orders'];
        foreach ($orderCollectionByDate as $key => $value) {
            $data[] = [date($date_format, strtotime($value['shipping_arrival_date'])), (int)$value['total_orders']];
        }
        $this->getResponse()->appendBody(json_encode($data));
    }
}
