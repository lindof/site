<?php
/**
 * Cminds Marketplace SetShippingPrice Controller.
 *
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Cminds Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Controller\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Cminds\Marketplace\Helper\Supplier;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Cminds\Supplierfrontendproductuploader\Helper\Price;

class SetShippingPrice extends Action
{
    protected $session;
    protected $supplierHelper;
    protected $jsonResultFactory;
    protected $priceHelper;

    /**
     * SetShippingPrice constructor.
     *
     * @param Context $context
     * @param Session $session
     * @param Supplier $supplierHelper
     * @param JsonFactory $jsonFactory
     * @param Price $price
     */
    public function __construct(
        Context $context,
        Session $session,
        Supplier $supplierHelper,
        JsonFactory $jsonFactory,
        Price $price
    ) {
        parent::__construct($context);

        $this->session = $session;
        $this->supplierHelper = $supplierHelper;
        $this->jsonResultFactory = $jsonFactory;
        $this->priceHelper = $price;
    }

    /**
     * SetShippingPrice controller
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $params = $this->getRequest()->getParams();

        $price = $params['price'];
        $methodId = $params['method_id'];
        $supplierId = $params['supplier_id'];

        $selected = $this->session->getMarketplaceShippingMethods();
        $selected[$supplierId] = [
            'method_id' => $methodId,
            'price' => $price
        ];

        $priceTotal = $this->supplierHelper
            ->calculateTotalShippingPrice($selected);

        $priceTotal = $this->priceHelper->convertToCurrentCurrencyPrice($priceTotal);
        $this->session
            ->setMarketplaceShippingMethods($selected)
            ->setMarketplaceShippingPrice($this->priceHelper->convertToBaseCurrencyPrice($priceTotal));

        $output = ['price_total' => $priceTotal];

        return $result->setData(
            $output
        );
    }
}