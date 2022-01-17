<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Block\Shippingterms;

use Cminds\Marketplace\Helper\Supplier;

/**
 * Class Termsandconditions
 *
 * @package Cminds\Marketplace\Block\Shippingterms
 */
class Termsandconditions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Supplier
     */
    protected $supplierHelper;

    /**
     * Termsandconditions constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Supplier $supplierHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Supplier $supplierHelper
    ) {
        parent::__construct($context);
        $this->supplierHelper = $supplierHelper;
    }

    /**
     * @return bool|string
     */
    public function getTermsAndConditions()
    {
        $supplierId = $this->getRequest()->getParam('id');
        if ($supplierId) {
            return $this->supplierHelper->getShippingTermsForSupplier($supplierId);
        }

        return false;
    }
}
