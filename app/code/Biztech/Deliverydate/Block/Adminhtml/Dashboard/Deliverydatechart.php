<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Dashboard\Grid as DashboardGrid;

class Deliverydatechart extends DashboardGrid
{

    public function getUrlJ()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_STATIC
        ) . 'adminhtml/Magento/backend/en_US/Biztech_Deliverydate/js/jsapi';
    }

    public function getUrlJSURL()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_STATIC
        ) . 'adminhtml/Magento/backend/en_US/Biztech_Deliverydate/js/jquery.min.js';
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('deliverydatecalendar');
        $this->setDestElementId('deliverydatecalendar_content');
        $this->setTemplate('Biztech_Deliverydate::dashboard/charts.phtml');
    }
}
