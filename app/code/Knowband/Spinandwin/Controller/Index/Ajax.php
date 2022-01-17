<?php

namespace Knowband\Spinandwin\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Knowband\Spinandwin\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
    }

    public function execute() {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            if (isset($post_data['method'])) {
                if ($post_data['method'] == 'checkWinningAndGenerateCoupon') {
                    $cus_email = $post_data['customer_email'];
                    $fname = $post_data['customer_fname'];
                    $lname = $post_data['customer_lname'];
                    $json_array =  $this->sp_helper->checkSlicesAndGenerateCoupon($cus_email,$fname,$lname);
                } else if ($post_data['method'] == 'sendEmailWithCouponCode') {
                    $cus_email = $post_data['customer_email'];
                    $coupon_code = $post_data['coupon_code'];
                    
                    $json_array =  $this->sp_helper->sendEmailWithCouponCode($cus_email, $coupon_code);
                }
            }
        }
//        $response_array = array();
//        $coupon_code = $this->sp_helper->generateRandomCouponCode();
//                
//        if ($this->getRequest()->isPost()) {
//            $check_rule_creation = $this->sp_helper->createCartRule(
//                $coupon_code,
//                1,
//                'percent',
//                20,
//                false,
//                true
//            );
//            if ($check_rule_creation) {
//                $response_array['coupon_code'] = $coupon_code;
//                $response_array['label'] = '';
//            } else {
//                $response_array['coupon_code'] = '';
//                $response_array['coupon_error'] = __('Something went wrong while generating the coupon code, please try again.');
//            }
//        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($json_array);
        return $resultJson;
    }
}
