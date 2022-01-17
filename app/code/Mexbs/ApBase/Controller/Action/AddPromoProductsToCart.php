<?php
namespace Mexbs\ApBase\Controller\Action;

use Magento\Catalog\Api\ProductRepositoryInterface;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

class AddPromoProductsToCart extends AddPromoProductAbstract
{
    protected $resultJsonFactory;
    protected $cart;
    protected $helper;
    protected $ruleFactory;
    protected $productRepository;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mexbs\ApBase\Helper\Data $helper
    ){
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->helper = $helper;
        $this->ruleFactory = $ruleFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }



    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $productsAddData = json_decode($params['products_add_data'], true);

        foreach($productsAddData as $productAddData){
            $product = $this->_initProduct($productAddData['product_id']);

            $productAddToCartParams = $this->getProductAddToCartData($productAddData);

            $this->cart->addProduct($product, $productAddToCartParams);

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
        }
        $this->cart->save();

        $this->_view->loadLayout();
        return $this->resultJsonFactory->create()->setData([
            'status' => 'success',
            'cart_html' => $this->_view->getLayout()->getBlock('checkout.cart')->toHtml()
        ]);
    }
}