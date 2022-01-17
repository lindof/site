<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;
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
        $t_data = array();
        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            $theme = $post_data['theme'];
            $t_data = $this->sp_helper->getEmailTemplateData($theme);
            $params = array('_secure' => $this->sp_request->isSecure());
            $email_img_loc  = $this->assetRepo->getUrlWithParams('Knowband_Spinandwin', $params).'/images/email/';
            $t_data['template_content'] = $this->sp_helper->replaceEmailContentImages($t_data['template_content'], $email_img_loc);
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($t_data);
        return $resultJson;
    }
}
