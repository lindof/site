<?php

namespace Biztech\Deliverydate\Plugin\Order\Create;

class Deliverydate extends \Magento\Backend\Block\Template
{

    /**
     * @param $subject
     * @param string $alias
     * @param bool|false $useCache
     * @return array
     */
    public function aftergetChildHtml($subject,  $result, $alias = '', $useCache = false)
    {
        if ($alias === 'gift_options') {
            $res = $this->getLayout()
                ->createBlock('\Magento\Backend\Block\Template')
                ->setNameInLayout('order-delivery-date')
                ->setTemplate('Biztech_Deliverydate::sales/order/create/admindelivery.phtml')
                ->toHtml();
            return $res . $result;
        } else {
            return $result;
        }
    }
}
