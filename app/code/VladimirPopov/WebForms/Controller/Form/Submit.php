<?php
/**
 * @author      Vladimir Popov
 * @copyright   Copyright © 2019 Vladimir Popov. All rights reserved.
 */

namespace VladimirPopov\WebForms\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\HTTP\Header;

class Submit extends Action
{
    protected $_coreRegistry;

    protected $resultPageFactory;

    protected $resultHttpFactory;

    protected $_jsonEncoder;

    protected $_formFactory;

    protected $_resultFactory;

    protected $_filterProvider;

    protected $messageManager;

    protected $_storeManager;

    protected $_customerSession;

    protected $_customerSessionFactory;

    protected $header;

    public function __construct(
        Context $context,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        PageFactory $resultPageFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\HttpFactory $resultHttpFactory,
        \VladimirPopov\WebForms\Model\FormFactory $formFactory,
        \VladimirPopov\WebForms\Model\ResultFactory $resultFactory,
        \Magento\Store\Model\StoreManager $storeManager,
        SessionFactory $customerSessionFactory,
        Header $header
    )
    {
        $this->resultPageFactory       = $resultPageFactory;
        $this->_coreRegistry           = $coreRegistry;
        $this->resultHttpFactory       = $resultHttpFactory;
        $this->_jsonEncoder            = $jsonEncoder;
        $this->_formFactory            = $formFactory;
        $this->_resultFactory          = $resultFactory;
        $this->_filterProvider         = $filterProvider;
        $this->messageManager          = $context->getMessageManager();
        $this->_storeManager           = $storeManager;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->header                  = $header;
        $this->_url                    = $context->getUrl();

        parent::__construct($context);
    }

    public function execute()
    {
        $webform = $this->_formFactory->create()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->load($this->getRequest()->getParam("webform_id"));

        // Ajax submit
        if ($this->getRequest()->getParam('ajax')) {
            $result = ["success" => false, "errors" => []];
            if ($this->getRequest()->getParam('submitForm_' . $webform->getId()) && $webform->getIsActive()) {
                $resultObject = $webform->savePostResult();
                if ($resultObject) {
                    $result["success"] = true;
                    if ($webform->getSuccessText()) $result["success_text"] = $webform->getSuccessText();

                    // apply custom variables
                    $filter        = $this->_filterProvider->getPageFilter();
                    $webformObject = new \Magento\Framework\DataObject;
                    $webformObject->setData($webform->getData());
                    $subject = $resultObject->getEmailSubject('customer');
                    $filter->setVariables(array(
                        'webform_result' => $resultObject->toHtml('customer'),
                        'result' => $resultObject->getTemplateResultVar(),
                        'webform' => $webformObject,
                        'webform_subject' => $subject
                    ));
                    $result["success_text"] = "&nbsp;";
                    if ($webform->getSuccessText()) {
                        $result["success_text"] = $filter->filter($webform->getSuccessText());
                    }

                    if ($webform->getRedirectUrl()) {
                        if (strstr($webform->getRedirectUrl(), '://'))
                            $redirectUrl = $webform->getRedirectUrl();
                        else
                            $redirectUrl = $this->_url->getUrl($webform->getRedirectUrl());
                        $result["redirect_url"] = $redirectUrl;
                    }
                } else {
                    $errors = $this->messageManager->getMessages(true)->getItems();
                    foreach ($errors as $err) {
                        $result["errors"][] = $err->getText();
                    }
                    $html_errors = "";
                    if (count($result["errors"]) > 1) {
                        foreach ($result["errors"] as $err) {
                            $html_errors .= '<p>' . $err . '</p>';
                        }
                        $result["errors"] = $html_errors;
                    } else {
                        $result["errors"] = $result["errors"][0];
                    }
                }
            }

            if (!$webform->getIsActive()) $result["errors"][] = __('Web-form is not active.');

            $json       = $this->_jsonEncoder->encode($result);
            $resultHttp = $this->resultHttpFactory->create();
            $resultHttp->setNoCacheHeaders();
            $resultHttp->setHeader('Content-Type', 'text/plain', true);
            return $resultHttp->setContent($json);
        }

        // regular submit

        if ($this->getRequest()->getParam('submitForm_' . $webform->getId()) && $webform->getIsActive()) {

            //validate
            $result = $webform->savePostResult();
            if ($result) {
                $this->_customerSession = $this->_customerSessionFactory->create();
                $this->_customerSession->setFormSuccess($webform->getId());
                $this->_customerSession->setData('webform_result_' . $webform->getId(), $result->getId());
            }

            $url = $this->header->getHttpReferer();

            if ($webform->getRedirectUrl()) {
                if (strstr($webform->getRedirectUrl(), '://')) {
                    $url = $webform->getRedirectUrl();
                } else {
                    $url = $this->_url->getUrl($webform->getRedirectUrl());
                }
            }

            return $this->_response->setRedirect($url);

        }
        $resultLayout = $this->resultPageFactory->create();
        $resultLayout->setStatusHeader(404, '1.1', 'Not Found');
        $resultLayout->setHeader('Status', '404 File not found');
        return $resultLayout;
    }
}
