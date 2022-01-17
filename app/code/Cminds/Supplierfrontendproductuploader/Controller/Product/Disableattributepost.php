<?php


namespace Cminds\Supplierfrontendproductuploader\Controller\Product;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class Disableattributepost extends AbstractController
{
    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    private $jsonSerializer;

    private $customerRepository;

    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        Json $jsonSerializer,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $helper, $storeManager, $scopeConfig);
        $this->customerSession = $customerSession;
        $this->jsonSerializer  = $jsonSerializer;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }
        $customer = $this->customerRepository->getById($this->customerSession->getCustomer()->getId());

        $disableAttributes = $this->getRequest()->getParam('attribute');
        if (!count($disableAttributes)) {
            $disableValues = '';
        } else {
            $disableValues = $this->jsonSerializer->serialize($disableAttributes);
        }
        $customer->setCustomAttribute('supplier_disable_attribute_values', $disableValues);
        $this->helper->disableProductsByAttribute($customer);
        $this->customerRepository->save($customer);
        $this->_redirect('supplier/product/disableattribute');
    }
}
