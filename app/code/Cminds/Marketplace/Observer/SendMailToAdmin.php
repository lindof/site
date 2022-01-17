<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Email\Model\BackendTemplate;
use Cminds\Supplierfrontendproductuploader\Helper\Data as Helper;

/**
 * Class SendMailToAdmin
 * @package Cminds\Marketplace\Observer
 */
class SendMailToAdmin implements ObserverInterface
{
    //General Contact EMAIL
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    const GENERAL_ADMIN_EMAIL = 'trans_email/ident_general/email';
    const GENERAL_ADMIN_NAME = 'trans_email/ident_general/name';

    //Customer Support EMAIL
    const XML_PATH_SUPPORT_EMAIL_RECIPIENT = 'trans_email/ident_support/email';
    const GENERAL_SUPPORT_EMAIL = 'trans_email/ident_support/email';
    const GENERAL_SUPPORT_NAME = 'trans_email/ident_support/name';

    // email template
    const SUPPLIER_REGISTER_EMAIL_TEMPLATE = 'new_supplier_registration';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BackendTemplate
     */
    protected $emailTemplate;

    /**
     * @var Helper
     */
    protected $supplierHelper;

    /**
     * SendMailToAdmin constructor.
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param BackendTemplate $emailTemplate
     * @param Helper $supplierHelper
     */
    public function __construct(
         TransportBuilder $transportBuilder,
         StateInterface $inlineTranslation,
         ScopeConfigInterface $scopeConfig,
         StoreManagerInterface $storeManager,
         BackendTemplate $emailTemplate,
         Helper $supplierHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->emailTemplate = $emailTemplate;
        $this->supplierHelper = $supplierHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->scopeConfig->getValue('configuration/configure/module_enabled')) {
            return $this;
        }

        if (!$this->scopeConfig->getValue('configuration/configure/supplier_needs_to_be_approved')) {
            return $this;
        }

        $customer = $observer->getData('customer');
        $customerGroup = $customer->getGroupId();

        $store = $this->storeManager->getStore()->getId();

        $adminEmail = $this->scopeConfig->getValue(self::GENERAL_ADMIN_EMAIL);
        $adminName = $this->scopeConfig->getValue(self::GENERAL_ADMIN_NAME);

        $emailTemplateId = self::SUPPLIER_REGISTER_EMAIL_TEMPLATE;

        $templateVars = [
            'admin_name' => $adminName,
            'supplier_first_name' => $customer->getFirstName(),
            'supplier_last_name' => $customer->getLastName(),
            'supplier_email' => $customer->getEmail(),
        ];

        $this->inlineTranslation->suspend();

        try {
             //Set sender to Admin!!!
            $sender = [
                'name' => $adminName,
                'email' => $adminEmail,
            ];

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($templateVars);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplateId, $storeScope)   // Send the ID of Email template which is created in Admin panel
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,     // using frontend area to get the template file
                        'store' => $store
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($this->scopeConfig->getValue(self::XML_PATH_SUPPORT_EMAIL_RECIPIENT, $storeScope))
                ->getTransport();

            //Send E-mail to admin only for approving Supplier registration and not for ordinary customers
            $customerGroups = $this->supplierHelper->getAllowedGroups();
            if (in_array($customerGroup, $customerGroups)) {
                $transport->sendMessage();
            }

            $this->inlineTranslation->resume();
        }
        catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
        }
    }
}