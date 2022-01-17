<?php
namespace Mexbs\ApBase\Controller\Action;

abstract class AddPromoProductAbstract extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $cart;
    protected $helper;
    protected $ruleFactory;
    protected $productRepository;
    protected $storeManager;

    protected function _initProduct($productId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    protected function getProductAddToCartData($productAddData){
        $productAddToCartParams = [
            'product_id' => $productAddData['product_id'],
            'qty' => (isset($productAddData['qty']) ? $productAddData['qty'] : 1),
            'super_attribute' => []
        ];

        if(isset($productAddData['options'])){
            foreach($productAddData['options'] as $productAddDataOptionKey => $productAddDataOptionVal){
                if(isset($productAddDataOptionVal['attribute_id'])){
                    $productAddToCartParams['super_attribute'][$productAddDataOptionVal['attribute_id']] = $productAddDataOptionVal['option_id'];
                }else{
                    if(!isset($productAddToCartParams['options'])){
                        $productAddToCartParams['options'] = [];
                    }
                    $productAddToCartParams['options'][$productAddDataOptionKey] = $productAddDataOptionVal;
                }
            }
        }

        if(isset($productAddData['bundle_option'])
            && isset($productAddData['bundle_option_qty'])){
            $productAddToCartParams['bundle_option'] = $productAddData['bundle_option'];
            $productAddToCartParams['bundle_option_qty'] = $productAddData['bundle_option_qty'];
        }

        return $productAddToCartParams;
    }
}