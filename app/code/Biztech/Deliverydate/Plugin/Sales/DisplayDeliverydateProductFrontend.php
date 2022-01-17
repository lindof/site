<?php

namespace Biztech\Deliverydate\Plugin\Sales;

/**
 * Class DisplayDeliverydateProductFrontend
 * @package Biztech\Deliverydate\Model\Sales
 */
class DisplayDeliverydateProductFrontend
{
    protected $http;
    protected $state;
    protected $scopeConfigInterface;

    /**
     * @param \Magento\Framework\App\State                       $state
     * @param \Magento\Framework\App\Request\Http                $http
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $ScopeConfigInterface
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Request\Http $http,
        \Magento\Framework\App\Config\ScopeConfigInterface $ScopeConfigInterface
    ) {
        $this->scopeConfigInterface = $ScopeConfigInterface;
        $this->http = $http;
        $this->state = $state;
    }

   
    /**
     * we append delivery date to in the product items of the order
     *
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetItemOptions(\Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject, $result)
    {
        $areaCode = $this->state->getAreaCode();
        $requestname = $areaCode ."_". $this->http->getFullActionName();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnable = $this->scopeConfigInterface->getValue('deliverydate/deliverydate_general/enabled', $storeScope);
        $enableview = $this->scopeConfigInterface->getValue('deliverydate/deliverydate_general/display_on', $storeScope);
        if (strlen($enableview) > 0) {
            $enableview = explode(",", $enableview);
        } else {
            $enableview = [];
        }
        $newResult = [];
        if (in_array($requestname, $enableview) === false && in_array("all", $enableview) === false) {
            foreach ($result as $key => $value) {
                if (array_key_exists("used_for", $value)) {
                    if ($value['used_for'] === "deliverydate") {
                        continue;
                    }
                } else {
                    $newResult[] = $value;
                }
            }
            return $newResult;
        } else {
            return $result;
        }
    }
}
