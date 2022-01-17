<?php

namespace Cminds\Supplierfrontendproductuploader\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class Inventory extends AbstractHelper
{
    /**
     * Product metadata
     *
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Module Manager
     *
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * Inventory constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleManager $moduleManager
     * @param Context $context
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ModuleManager $moduleManager,
        Context $context
    ) {
        parent::__construct($context);

        $this->productMetadata = $productMetadata;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Check version for MSI
     *
     * @return bool
     */
    public function msiFunctionalitySupported()
    {
        return $this->moduleManager->isEnabled('Magento_InventoryReservations');
    }
}
