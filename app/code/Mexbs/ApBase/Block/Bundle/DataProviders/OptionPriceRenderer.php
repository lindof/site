<?php
namespace Mexbs\ApBase\Block\Bundle\DataProviders;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;

class OptionPriceRenderer implements \Magento\Framework\Data\CollectionDataSourceInterface{
    private $tierPriceLayout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(LayoutInterface $tierPriceLayout)
    {
        $this->tierPriceLayout = $tierPriceLayout;
    }

    public function renderTierPrice(Product $selection, array $renderArguments = [])
    {
        if (!array_key_exists('zone', $renderArguments)) {
            $renderArguments['zone'] = Render::ZONE_ITEM_OPTION;
        }

        $html = '';

        $render = $this->tierPriceLayout->getBlock('product.price.render.default');
        if ($render !== false) {
            $html = $render->render(
                TierPrice::PRICE_CODE,
                $selection,
                $renderArguments
            );
        }

        return $html;
    }
}
