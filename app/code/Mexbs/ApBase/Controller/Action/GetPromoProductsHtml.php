<?php
namespace Mexbs\ApBase\Controller\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

class GetPromoProductsHtml extends \Magento\Framework\App\Action\Action
{
    private $resultJsonFactory;
    private $cart;
    private $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Mexbs\ApBase\Helper\Data $helper
    ){
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $currentProductId = $this->getRequest()->getParam('product_id');
        $location = $this->getRequest()->getParam('location', \Mexbs\ApBase\Helper\Data::PROMO_BLOCK_LOCATION_CART);
        $quote = $this->cart->getQuote();

        $this->_view->loadLayout();

        $rulesHtml = $this->helper->getPromoBlockRulesHtmlArray($quote, $currentProductId, $location);
        return $this->resultJsonFactory->create()->setData($rulesHtml);
    }
}