<?php

namespace Orange35\Colorpickercustom\Controller\Adminhtml\Image;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Orange35\Colorpickercustom\Model\Uploader;

class Upload extends Action
{
    const ACTION_RESOURCE = 'Orange35_Colorpickercustom::image';

    private $uploader;

    public function __construct(Context $context, Uploader $uploader)
    {
        parent::__construct($context);
        $this->uploader = $uploader;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }

    public function execute()
    {
        try {
            $result = $this->uploader->saveFileToTmpDir($this->getFieldName());

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

    private function getFieldName()
    {
        return $this->_request->getParam('field');
    }
}
