<?php
namespace Mexbs\ApBase\Controller\Adminhtml\Promo\Quote\Image;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    private $imageUploader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mexbs\ApBase\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    protected function getImageId(){
        return 'apbase_image';
    }

    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDirectory($this->getImageId());

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
