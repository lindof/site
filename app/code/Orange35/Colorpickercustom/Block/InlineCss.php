<?php

namespace Orange35\Colorpickercustom\Block;

use Magento\Framework\View\Element\Template;
use Orange35\Colorpickercustom\Helper\ConfigHelper;

class InlineCss extends Template
{
    /** @var int padding which is set in the Magento_Swatches::css/swatches.css */
    const TOOLTIP_DEFAULT_PADDING = 5;

    /** @var int extra tooltip width in comparison with image width */
    const TOOLTIP_EXTRA_MAX_WIDTH = 30;

    /** @var int extra tooltip height in comparison with image height */
    const TOOLTIP_EXTRA_MAX_HEIGHT = 70;

    private $configHelper;
    private $assetCollection;
    private $productMetadata;

    public function __construct(
        Template\Context $context,
        ConfigHelper $configHelper,
        \Magento\Framework\View\Asset\GroupedCollection $assetCollection,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->assetCollection = $assetCollection;
        $this->productMetadata = $productMetadata;
    }

    protected function _prepareLayout()
    {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.3.0', '<')) {
            $this->addCss('Magento_Swatches::css/swatches.css');
        }
        $this->addCss('Orange35_Colorpickercustom::css/slick.css');
        $this->addCss('Orange35_Colorpickercustom::css/swatches.css');

        return parent::_prepareLayout();
    }

    private function addCss($id)
    {
        $asset = $this->_assetRepo->createAsset($id);
        $this->assetCollection->add($id, $asset);
    }

    public function getSwatchOptionCss()
    {
        $css = [];
        if (($width = $this->configHelper->getSwatchWidth())) {
            $css[] = 'width: ' . $width . 'px!important';
        }
        if (($height = $this->configHelper->getSwatchHeight())) {
            $css[] = 'height: ' . $height . 'px!important; line-height: ' . $height . 'px!important';
        }

        return implode('; ', $css);
    }

    public function getTooltipImageCss()
    {
        $css = [];
        if (($width = $this->configHelper->getTooltipWidth())) {
            $css[] = 'width: ' . $width . 'px';
        }
        if (($height = $this->configHelper->getTooltipHeight())) {
            $css[] = 'height: ' . $height . 'px;';
        }

        return implode('; ', $css);
    }

    public function getTooltipMaxWidth()
    {
        if (($width = $this->configHelper->getTooltipWidth())) {
            return $width + self::TOOLTIP_EXTRA_MAX_WIDTH
                + $this->configHelper->getTooltipPadding() - self::TOOLTIP_DEFAULT_PADDING;
        }
        return null;
    }

    private function getTooltipMaxHeight()
    {
        if (($height = $this->configHelper->getTooltipHeight())) {
            return $height + self::TOOLTIP_EXTRA_MAX_HEIGHT
                + $this->configHelper->getTooltipPadding() - self::TOOLTIP_DEFAULT_PADDING;
        }
        return null;
    }

    public function getTooltipCss()
    {
        $css = [];
        if (($value = $this->getTooltipMaxWidth())) {
            $css[] = 'max-width: ' . $value . 'px';
        }
        if (($value = $this->getTooltipMaxHeight())) {
            $css[] = 'max-height: ' . $value . 'px';
        }
        if (null !== ($value = $this->configHelper->getTooltipPadding())) {
            // add padding to a top, left, right but not bottom since it overlap swatch and causes flickering
            $css[] = "padding: {$value}px {$value}px " . self::TOOLTIP_DEFAULT_PADDING . "px {$value}px";
        }
        return implode('; ', $css);
    }

    public function getSwatchSelectedOutlineCss()
    {
        $css = [];
        if (($outlineWidth = $this->configHelper->getSwatchSelectedOutlineWidth())) {
            $css[] = 'outline-width: ' . $outlineWidth . 'px';
            if (($outlineColor = $this->configHelper->getSwatchSelectedOutlineColor())) {
                $css[] = 'outline-color: ' . $outlineColor;
            }
        }
        return implode('; ', $css);
    }

    public function getConfigHelper()
    {
        return $this->configHelper;
    }
}
