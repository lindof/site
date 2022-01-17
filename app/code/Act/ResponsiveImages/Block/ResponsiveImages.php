<?php
namespace Act\ResponsiveImages\Block;

class ResponsiveImages extends \Magento\Framework\View\Element\Template 
{

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		 \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface)
	{   
		$this->StoreManagerInterface=$StoreManagerInterface;
		parent::__construct($context);
	}
       
    public function getMediaUrl()
    {
        $media_url=$this->StoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_url;
    }

}