<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Controller\Shipment;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Cminds\Marketplace\Model\Fields;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\OrderRepository as OrderRepository;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Email
 * @package Cminds\Marketplace\Controller\Shipment
 */
class Email extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $supplierFrontendProductUploaderHelperData;

    /**
     * Address renderer object.
     *
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * Fields object.
     *
     * @var Fields
     */
    protected $fields;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Email constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param Data $data
     * @param AddressRenderer $addressRenderer
     * @param Fields $fields
     * @param \Magento\Framework\App\Request\Http $request
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSession $customerSession
     * @param OrderRepository $orderRepository
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Data $data,
        AddressRenderer $addressRenderer,
        Fields $fields,
        \Magento\Framework\App\Request\Http $request,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        OrderRepository $orderRepository,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    )
    {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->supplierFrontendProductUploaderHelperData = $data;
        $this->addressRenderer = $addressRenderer;
        $this->fields = $fields;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        $order = $this->orderRepository->get($orderId);

        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($order->getWebsiteId());
        $customer = $this->customerRepository->getById($this->customerSession->getId());

        if ($this->customerSession->isLoggedIn()) {
            try {
                $emailShippingCompany = $customer->getCustomAttribute('email_shipping_company');
                if ($emailShippingCompany) {
                    $email = $emailShippingCompany->getValue();
                } else {
                    $this->messageManager->addNoticeMessage(__('Shipping company Email of this Supplier is empty.'));

                    return $this->_redirect(
                        '*/order/view/',
                        ['id' => $orderId]
                    );
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        /* Here we prepare data for our email  */

        /* Receiver Detail  */
        $receiverInfo = [
            'name' => $email,
            'email' => $email
        ];

        /* Support Email */
        $adminEmail = $this->scopeConfig->getValue('trans_email/ident_custom2/email',ScopeInterface::SCOPE_STORE);
        $adminName  = $this->scopeConfig->getValue('trans_email/ident_custom2/name',ScopeInterface::SCOPE_STORE);

        /* Sender Detail  */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];

        /* Assign values for your template variables  */
        $supplierId=$this->customerSession->getId();

        $emailTemplateVariables = [];
        $emailTempVariables['formattedShippingAddress'] = $this->getFormattedShippingAddress($order);
        $emailTempVariables['formattedBillingAddress'] = $this->getFormattedBillingAddress($order);
        $emailTempVariables['order'] = $order;
        $emailTempVariables['order_data'] = $order;
        $emailTempVariables['supplier_name'] = $this->getSupplierName($customer);
        $emailTempVariables['supplier_address'] = $this->getCustomFieldsValues($customer,'adresse'); // adresse - custom field
        $emailTempVariables['supplier_logo'] = $this->supplierFrontendProductUploaderHelperData->getSupplierLogo($supplierId);
        $emailTempVariables['supplier_email'] = $customer->getEmail();

        /* We write send mail function in helper because if we want to
           use same in other action then we can call it directly from helper */
        try {
            $this->_objectManager->get('Cminds\Marketplace\Helper\Email')->ShippingCompany_MailSendMethod(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo
            );
            $this->messageManager->addSuccess(__('You sent the order email to Shipping Company.').' (' .$email .')');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t send the email order right now.'));
        }

        return $this->_redirect(
            '*/order/view/',
            ['id' => $orderId]
        );
    }

    /**
     * @param $supplier
     * @return string
     */
    protected function getSupplierName($supplier)
    {
        $supplierName = $supplier->getFirstname() . ' ' . $supplier->getLastname();
        if (empty($supplier->getCustomAttributes()) || empty($supplier->getCustomAttributes()['supplier_name'])) {
            return $supplierName;
        }

       return $supplier->getCustomAttributes()['supplier_name']->getValue();
    }

    /**
     * Get shipping address.
     *
     * @return false|string
     */
    public function getFormattedShippingAddress($order)
    {
        if ($order->getShippingAddress() !== null) {
            return $this->addressRenderer->format(
                $order->getShippingAddress(),
                'html'
            );
        }

        return false;
    }

    /**
     * Get billing address.
     *
     * @return null|string
     */
    public function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format(
            $order->getBillingAddress(),
            'html'
        );
    }

    /**
     * @param $customer
     * @param $name
     * @return string
     */
    public function getCustomFieldsValues($customer,$name )
    {
        if (!isset($customer->getCustomAttributes()['custom_fields_values'])) {
            return '';
        }

        $values = $customer->getCustomAttributes()['custom_fields_values'];
        $dbValues = unserialize($values->getValue());
        $ret = [];
        $retName = 'Empty';

        if (!$dbValues) {
            return '';
        }

        foreach ($dbValues AS $value) {
            $v = $this->fields->load($value['name'], 'name');
  
            if (isset($v)) {
                if ($value['name'] == $name) {
                    $ret[] = $value;
                    $retName = $value['value'];
                }
            }
        }

        return $retName;
    }
}