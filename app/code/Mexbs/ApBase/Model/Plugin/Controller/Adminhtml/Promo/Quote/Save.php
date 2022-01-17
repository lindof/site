<?php
namespace Mexbs\ApBase\Model\Plugin\Controller\Adminhtml\Promo\Quote;

class Save
{
    protected $apHelper;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper
    ) {
        $this->apHelper = $apHelper;
    }

    public function beforeExecute(\Magento\SalesRule\Controller\Adminhtml\Promo\Quote\Save $subject){
        $requestData = $subject->getRequest()->getPostValue();


        $requestData = $this->apHelper->addActionDetailsToRequestData($requestData);
        $requestData = $this->apHelper->prepareImageRequestData($requestData);

        $subject->getRequest()->setPostValue($requestData);
    }
}