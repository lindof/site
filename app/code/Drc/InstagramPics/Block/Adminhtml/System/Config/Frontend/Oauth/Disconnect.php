<?php

namespace Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend\Oauth;

use Magento\Backend\Block\Template;

class Disconnect extends Template
{
    public function getButton()
    {
        return '<button type="button" 
		            id="instagrampics_oauth" 
					class="scalable " 
					style="width:280px" 
					onclick="" 
					title="Connect">
					<span>Disonnect</span>
				</button>';
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        return '<button type="button" 
		            id="instagrampics_oauth" 
					class="scalable " 
					style="width:280px" 
					onclick="" 
					title="Connect">
					<span>Disonnect</span>
				</button>';
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        return parent::getContainerId();
    }

    public function getDisconnectUrl()
    {
        return $this->_urlBuilder->getUrl('drc_instagrampics/index/disconnect');
    }
}
