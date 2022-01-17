<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;
use Magento\Framework\Controller\ResultFactory;

class TestEmail extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->assetRepo = $assetRepo;
    }

    public function execute() {
        $response_array = array();
        if ($this->getRequest()->isPost()) {
            $response_array = array();
            $post_data = $this->getRequest()->getPost();
            $coupon_code = 'XXXXXXXXXXXXXX';
            $template_data = array();
            $template_data['template_content'] = $post_data['html_content'];
            $template_data['template_subject'] = $post_data['subject'];
            $template_data['scope'] = $post_data['scope'];
            $template_data['scope_id'] = $post_data['scope_id'];
            if ($this->sp_helper->sendSpinAndWinCouponEmail($post_data['email'], $coupon_code, $template_data)) {
                $response_array['success'] = __('Email has been sent successfully.');
            } else {
                $response_array['error'] = __('Something went wrong while sending the coupon email, please try again.');
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response_array);
        return $resultJson;
    }
}
