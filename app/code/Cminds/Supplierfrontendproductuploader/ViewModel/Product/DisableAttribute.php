<?php

namespace Cminds\Supplierfrontendproductuploader\ViewModel\Product;

use Cminds\Supplierfrontendproductuploader\Model\Config;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Session as CustomerSession;

class DisableAttribute implements ArgumentInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var Config
     */
    protected $config;

    protected $attribute;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var SupplierHelper
     */
    protected $supplierHelper;

    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        Config $config,
        CustomerSession $customerSession,
        SupplierHelper $supplierHelper
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->config              = $config;
        $this->customerSession     = $customerSession;
        $this->supplierHelper      = $supplierHelper;
    }

    public function getAttribute()
    {
        if (!$this->attribute) {
            $this->attribute = $this->attributeRepository->get(
                \Magento\Catalog\Model\Product::ENTITY,
                $this->config->getDisableAttributeCode()
            );
        }
        return $this->attribute;
    }

    public function getAttributeValues()
    {
        $disabledAttributes = $this->supplierHelper->getSupplierDisableAttributeValues(
            $this->customerSession->getCustomer()->getDataModel()
        );
        $disabledAttributes = array_flip($disabledAttributes);
        $options = $this->getAttribute()->getSource()->getAllOptions(false);

        foreach ($options as &$option) {
            $option['disabled'] = array_key_exists($option['value'], $disabledAttributes) ? true : false;
        }

        return $options;
    }
}
