<?php

namespace Act\StockMessage\Plugin\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Configurable {

    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $stockRegistry;

    /**
     * Treshold configuration for instance the message will show when
     * total quantity rules apply to threshold.
     */
    const STOCK_TRESHOLD_LESS_THAN_N = 5;

    const STOCK_TRESHOLD_EQUALS_N = 1;

    /**
     * Stock messages
     */
    const MESSAGE_STOCK_LESS_THAN_N = 'Hurry only a few left';

    const MESSAGE_STOCK_EQUALS_N = 'Hurry only %1 remaining';

    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        StockRegistryInterface $stockRegistry
    ) {

        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->stockRegistry = $stockRegistry;
    }

    // Adding Quantitites (product=>qty)
    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    ) {
        $quantities = [];
        $attribute_sets = [];
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);


        foreach ($subject->getAllowProducts() as $product) {
            $stockitem = $this->stockRegistry->getStockItem(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
            $quantities[$product->getId()] = $stockitem->getQty();
            $attribute_sets[$product->getId()] = $product->getAttributeSetId();
        }

        $config['attributeSets'] = $attribute_sets;
        $config['quantities'] = $quantities;

        $config['stockTresholdLessThanN'] = $this::STOCK_TRESHOLD_LESS_THAN_N;
        $config['stockTresholdEqualsN'] = $this::STOCK_TRESHOLD_EQUALS_N;

        $config['stockMessages'] = [
            'lessThanN' => '<span class="act-stock-message">' . __($this::MESSAGE_STOCK_LESS_THAN_N) . '</span>',
            'equalsN' => '<span class="act-stock-message">' . __($this::MESSAGE_STOCK_EQUALS_N, [$this::STOCK_TRESHOLD_EQUALS_N]) . '</span>'
        ];


        return $this->jsonEncoder->encode($config);
    }
}
