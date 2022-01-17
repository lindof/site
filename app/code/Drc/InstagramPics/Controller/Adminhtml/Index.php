<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Drc\InstagramPics\Controller\Adminhtml;

/**
 * Newsletter subscribers controller
 */
abstract class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory = null
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
}
