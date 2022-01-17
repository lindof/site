<?php

namespace Biztech\Deliverydate\Component\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class OrderGrid extends Column
{
    public function __construct(
        ContextInterface $contextInterface,
        UiComponentFactory $componentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($contextInterface, $componentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_bizHelper = $objectManager->create('\Biztech\Deliverydate\Helper\Data');
        $config = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $dateFormat = $config->getValue('deliverydate/deliverydate_general/deliverydate_format');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $shippingArrivalDate = '';
                if ($item[$this->getData('name')] != '' && $item[$this->getData('name')] != '0000-00-00 00:00:00') {
                    $shippingArrivalDate = date($dateFormat, strtotime($item[$this->getData('name')]));
                }
                $item[$this->getData('name')] = $shippingArrivalDate;
            }
        }
        return $dataSource;
    }
}
