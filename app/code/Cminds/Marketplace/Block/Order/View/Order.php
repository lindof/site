<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Block\Order\View;

use Cminds\Marketplace\Helper\Data;
use Magento\Catalog\Model\ProductFactory as CatalogProduct;
use Magento\Directory\Model\Country;
use Magento\Framework\Pricing\Helper\Data as CurrencyHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Order
 * @package Cminds\Marketplace\Block\Order\View
 */
class Order extends Template
{
    /**
     * @var Data
     */
    protected $marketplaceHelper;

    /**
     * @var SalesOrder
     */
    protected $salesOrder;

    /**
     * @var CurrencyHelper
     */
    protected $currencyHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CatalogProduct
     */
    protected $product;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Order constructor.
     * @param Context $context
     * @param Registry $registry
     * @param SalesOrder $salesOrder
     * @param CatalogProduct $product
     * @param Data $marketplaceHelper
     * @param Country $country
     * @param CurrencyHelper $currencyHelper
     * @param Renderer $renderer
     * @param CustomerSession $customerSession
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SalesOrder $salesOrder,
        CatalogProduct $product,
        Data $marketplaceHelper,
        Country $country,
        CurrencyHelper $currencyHelper,
        Renderer $renderer,
        CustomerSession $customerSession,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->salesOrder = $salesOrder;
        $this->product = $product;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->country = $country;
        $this->currencyHelper = $currencyHelper;
        $this->renderer = $renderer;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return SalesOrder
     */
    public function getOrder()
    {
        $id = $this->registry->registry('order_id');

        return $this->salesOrder->load($id);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $id = $this->registry->registry('order_id');
        $order = $this->salesOrder->load($id);
        $items = [];

        $supplierId = $this->marketplaceHelper->getSupplierId();

        foreach ($order->getAllItems() as $item) {
            $product = $this->product->create([])->load($item->getProductId());

            if ($product->getCreatorId() === $supplierId) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param $code
     * @return string
     */
    public function getLoadByCountry($code)
    {
        return $this->country->load($code)->getName();
    }

    /**
     * @return CurrencyHelper
     */
    public function getCurrencyHelper()
    {
        return $this->currencyHelper;
    }

    /**
     * @return null|string
     */
    public function getFormatedShippingAddress()
    {
        return $this->renderer->format(
            $this->getOrder()->getShippingAddress(),
            'html'
        );
    }

    /**
     * @return null|string
     */
    public function getFormatedBillingAddress()
    {
        return $this->renderer->format(
            $this->getOrder()->getBillingAddress(),
            'html'
        );
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isEmailShippingCompany()
    {
        $order = $this->getOrder();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($order->getWebsiteId());
        $customer = $this->customerRepository->getById($this->customerSession->getId());

        if ($this->customerSession->isLoggedIn()) {
            if ($customer->getCustomAttribute('email_shipping_company')) {
                return true;
            }
        }

        return false;
    }
}
