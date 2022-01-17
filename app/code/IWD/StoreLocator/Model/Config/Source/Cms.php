<?php

namespace IWD\StoreLocator\Model\Config\Source;

/**
 * Class Cms
 * @package IWD\StoreLocator\Model\Config\Source
 */
class Cms implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\Block
     */
    private $block;

    /**
     * Cms constructor.
     * @param \Magento\Cms\Model\Block $block
     */
    public function __construct(\Magento\Cms\Model\Block $block)
    {
        $this->block = $block;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->block->getCollection()->addFieldToFilter('is_active', ['eq' => 1])->load();
        $items = [];
        $item = ['value' => '', 'label' =>__('-- Please Select CMS Block --')];
        $items[] = $item;

        foreach ($collection as $block) {
            $item = ['value' => $block->getIdentifier(), 'label' =>$block->getTitle()];
            $items[] = $item;
        }
        
        return $items;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Km'), 2 => __('Miles')];
    }
}
