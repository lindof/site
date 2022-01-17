<?php

namespace IWD\StoreLocator\Block;

/**
 * Class Link
 * "My Wish List" link
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
      
        return ($this->_isShown()) ? parent::_toHtml() : '';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        $path = $this->_scopeConfig->getValue('iwd_storelocator/general/path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $path = strtolower($path);
        $path = trim($path);
        return $this->getUrl($path);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->_scopeConfig->getValue('iwd_storelocator/general/link_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    private function _isShown()
    {
        return (bool)$this->_scopeConfig->getValue('iwd_storelocator/general/link_visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
