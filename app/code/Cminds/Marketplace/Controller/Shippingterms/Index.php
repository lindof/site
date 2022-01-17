<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Controller\Shippingterms;

use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Cminds\Marketplace\Controller\Shippingterms
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
        $result = $this->resultPageFactory->create();
        return $result;
    }
}
