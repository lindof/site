<?php

namespace Orange35\Colorpickercustom\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Swatches\Model\Swatch;

class ConfigHelper
{
    const SWATCH_CATEGORY_MODE          = 'colorpickercustom_section/swatch/category_mode';
    const SWATCH_CHOICE_METHOD          = 'colorpickercustom_section/swatch/choice_method';
    const SWATCH_WIDTH                  = 'colorpickercustom_section/swatch/swatch_width';
    const SWATCH_HEIGHT                 = 'colorpickercustom_section/swatch/swatch_height';
    const SWATCH_SELECTED_OUTLINE_WIDTH = 'colorpickercustom_section/swatch/selected_outline_width';
    const SWATCH_SELECTED_OUTLINE_COLOR = 'colorpickercustom_section/swatch/selected_outline_color';
    const SWATCH_SHOW_PRICE             = 'colorpickercustom_section/swatch/show_price';

    const SLIDER_ENABLED            = 'colorpickercustom_section/slider/enabled';
    const SLIDER_ARROWS_COLOR       = 'colorpickercustom_section/slider/arrows_color';
    const SLIDER_SWATCHES_PER_SLIDE = 'colorpickercustom_section/slider/swatches_per_slide';

    const TOOLTIP_WIDTH   = 'colorpickercustom_section/tooltip/width';
    const TOOLTIP_HEIGHT  = 'colorpickercustom_section/tooltip/height';
    const TOOLTIP_PADDING = 'colorpickercustom_section/tooltip/padding';

    private $scopeConfig;
    private $mediaHelper;

    public function __construct(ScopeConfigInterface $scopeConfig, \Magento\Swatches\Helper\Media $mediaHelper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->mediaHelper = $mediaHelper;
    }

    private function get($name)
    {
        return $this->scopeConfig->getValue($name, ScopeInterface::SCOPE_STORE);
    }

    private function getIntOrNull($name)
    {
        $value = $this->get($name);
        if (is_numeric($value)) {
            return (int) $value;
        }
        return null;
    }

    public function getCategoryMode()
    {
        return $this->get(self::SWATCH_CATEGORY_MODE);
    }

    public function getImageConfig()
    {
        return $this->mediaHelper->getImageConfig();
    }

    public function getSwatchChoiceMethod()
    {
        return $this->get(self::SWATCH_CHOICE_METHOD);
    }

    public function getSwatchWidth()
    {
        return $this->get(self::SWATCH_WIDTH);
    }

    public function getSwatchHeight()
    {
        return $this->get(self::SWATCH_HEIGHT);
    }

    public function getSwatchFinalWidth()
    {
        return $this->getSwatchWidth() ?: $this->getImageConfig()[Swatch::SWATCH_IMAGE_NAME]['width'];
    }

    public function getSwatchFinalHeight()
    {
        return $this->getSwatchHeight() ?: $this->getImageConfig()[Swatch::SWATCH_IMAGE_NAME]['height'];
    }

    public function getSwatchSelectedOutlineWidth()
    {
        return $this->get(self::SWATCH_SELECTED_OUTLINE_WIDTH);
    }

    public function getSwatchSelectedOutlineColor()
    {
        return $this->get(self::SWATCH_SELECTED_OUTLINE_COLOR);
    }

    public function getSwatchShowPrice()
    {
        return (bool) $this->get(self::SWATCH_SHOW_PRICE);
    }

    public function isSliderEnabled()
    {
        return (bool) $this->get(self::SLIDER_ENABLED);
    }

    public function getSliderArrowsColor()
    {
        return $this->get(self::SLIDER_ARROWS_COLOR);
    }

    public function getSwatchesPerSlide()
    {
        return (int) $this->get(self::SLIDER_SWATCHES_PER_SLIDE);
    }

    public function getTooltipWidth()
    {
        return $this->get(self::TOOLTIP_WIDTH);
    }

    public function getTooltipHeight()
    {
        return $this->get(self::TOOLTIP_HEIGHT);
    }

    public function getTooltipFinalWidth()
    {
        return $this->getTooltipWidth() ?: $this->getImageConfig()[Swatch::SWATCH_THUMBNAIL_NAME]['width'];
    }

    public function getTooltipFinalHeight()
    {
        return $this->getTooltipHeight() ?: $this->getImageConfig()[Swatch::SWATCH_THUMBNAIL_NAME]['height'];
    }

    public function getTooltipPadding()
    {
        return $this->getIntOrNull(self::TOOLTIP_PADDING);
    }
}
