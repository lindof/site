<?php
namespace Mexbs\ApBase\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    protected $apHelper;


    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper
    ) {
        $this->apHelper = $apHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'isApShowBreakdown' => $this->apHelper->getIsDiscountBreakdownEnabled(),
            'isApBreakdownCollapsedByDefault' => $this->apHelper->getIsDiscountBreakdownCollapsed()
        ];
    }
}
