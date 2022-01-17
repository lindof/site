<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer;

use Biztech\Deliverydate\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\StoreManagerInterface;

class Website extends Field
{

    protected $scopeConfig;
    protected $helper;
    protected $encrypt;
    protected $storeManager;

    /**
     * Website constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface $encrypt
     * @param \Biztech\Deliverydate\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encrypt,
        Data $helper,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->encrypt = $encrypt;
        $this->helper = $helper;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '';
        $data = $this->_scopeConfig->getValue('deliverydate/activation/data');
        $eleValue = explode(',', str_replace($data, '', $this->encrypt->decrypt($element->getValue())));
        $eleName = $element->getName();
        $eleId = $element->getId();
        $element->setName($eleName . '[]');
        $getDataInfo = $this->helper->getDataInfo();
        $dataInfo = (array)$getDataInfo;
        if (isset($getDataInfo->dom) && (int)($getDataInfo->c) > 0 && (int)($getDataInfo->suc) == 1) {
            foreach ($this->storeManager->getWebsites() as $website) {
                $url = $this->scopeConfig->getValue('web/unsecure/base_url');
                $url = $this->helper->getFormatUrl(trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url)));
                foreach ($getDataInfo->dom as $web) {
                    if ($web->dom == $url && $web->suc == 1) {
                        $element->setChecked(false);
                        $id = $website->getId();
                        $name = $website->getName();
                        $element->setId($eleId . '_' . $id);
                        $element->setValue($id);
                        if (in_array($id, $eleValue) !== false) {
                            $element->setChecked(true);
                        }
                        if ($id != 0) {
                            $html .= '<div><label>' . $element->getElementHtml() . ' ' . $name . ' </label></div>';
                        }
                    }
                }
            }
        } else {
            $html = sprintf('<strong class="required" style="color:red;">%s</strong>', __('Please enter a valid key'));
        }
        return $html;
    }
}
