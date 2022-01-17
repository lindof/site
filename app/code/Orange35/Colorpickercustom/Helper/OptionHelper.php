<?php

namespace Orange35\Colorpickercustom\Helper;

use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Swatches\Model\Swatch;
use Orange35\Colorpickercustom\Model\Config\Source\ChoiceMethod;
use Orange35\Colorpickercustom\Model\Config\Source\TooltipType;

class OptionHelper
{
    private $imageHelper;
    private $configHelper;
    private $priceHelper;

    public function __construct(Image $imageHelper, ConfigHelper $configHelper, PriceHelper $pricingHelper)
    {
        $this->imageHelper = $imageHelper;
        $this->configHelper = $configHelper;
        $this->priceHelper = $pricingHelper;
    }

    public function isMultiple(Option $option)
    {
        $type = $option->getType();
        return in_array($type, [Option::OPTION_TYPE_CHECKBOX, Option::OPTION_TYPE_MULTIPLE]);
    }

    public function getSwatchType(Value $value)
    {
        if ($this->getSwatchImage($value)) {
            return Swatch::SWATCH_TYPE_VISUAL_IMAGE;
        }
        if ($this->getSwatchColor($value)) {
            return Swatch::SWATCH_TYPE_VISUAL_COLOR;
        }
        return Swatch::SWATCH_TYPE_TEXTUAL;
    }

    /**
     * Return swatch type in order to show correct tooltip type
     * @param Value $value
     * @return int
     */
    public function getSwatchFinalType(Value $value)
    {
        switch ($this->getTooltipType($value->getOption())) {
            case TooltipType::TEXT:
                return Swatch::SWATCH_TYPE_TEXTUAL;
            case TooltipType::NONE:
                return Swatch::SWATCH_TYPE_VISUAL_IMAGE;
            default:
                return $this->getSwatchType($value);
        }
    }

    public function getSwatchTooltipImageUrl(Value $value)
    {
        if (!($image = $this->getSwatchImage($value))) {
            return null;
        }
        $width = $this->configHelper->getTooltipFinalWidth();
        $height = $this->configHelper->getTooltipFinalHeight();
        return $this->imageHelper->getImageUrl($image, $width, $height);
    }

    public function getSwatchImageUrl(Value $value)
    {
        if (!($image = $this->getSwatchImage($value))) {
            return null;
        }
        $option = $value->getOption();
        $width = $this->getSwatchFinalWidth($option);
        $height = $this->getSwatchFinalHeight($option);
        return $this->imageHelper->getImageUrl($image, $width, $height);
    }

    public function isColorPicker(Option $option)
    {
        return (bool) $option->getData('is_colorpicker');
    }

    public function getSwatchFinalTitle(Value $value)
    {
        $title = $value->getTitle();
        if ($this->configHelper->getSwatchShowPrice() && ($price = $value->getPrice()) > 0) {
            $title .= ' +' . $this->priceHelper->currency($price, true, false);
        }
        return $title;
    }

    public function getSwatchImage(Value $value)
    {
        return $value->getData('image');
    }

    public function getSwatchColor(Value $value)
    {
        return $value->getData('color');
    }

    public function getSwatchWidth(Option $option)
    {
        return $option->getData('swatch_width');
    }

    public function getSwatchHeight(Option $option)
    {
        return $option->getData('swatch_height');
    }

    public function getSwatchFinalWidth(Option $option)
    {
        return $this->getSwatchWidth($option) ?: $this->configHelper->getSwatchFinalWidth();
    }

    public function getSwatchFinalHeight(Option $option)
    {
        return $this->getSwatchHeight($option) ?: $this->configHelper->getSwatchFinalHeight();
    }

    public function isShownInList(Option $option)
    {
        return $option->getData('show_in_list');
    }

    public function getSwatchCss(Value $value)
    {
        $css = [];
        $option = $value->getOption();
        if (($width = $this->getSwatchWidth($option))) {
            $css[] = 'width: ' . $width . 'px';
        }
        if (($height = $this->getSwatchHeight($option))) {
            $css[] = 'height: ' . $height . 'px; line-height: ' . $height . 'px';
        }
        return implode('; ', $css) . (!empty($css) ? ';' : '');
    }

    public function getTooltipType(Option $option)
    {
        return (int) $option->getData('tooltip');
    }

    public function hasTooltip(Option $option)
    {
        return $this->getTooltipType($option) !== TooltipType::NONE;
    }

    /**
     * @param Product $product
     * @param Option[] $options
     * @return array
     */
    public function getJsonOptions($product, $options)
    {
        return [
            'SWATCH_TYPE_VISUAL_IMAGE' => Swatch::SWATCH_TYPE_VISUAL_IMAGE,
            'SWATCH_TYPE_VISUAL_COLOR' => Swatch::SWATCH_TYPE_VISUAL_COLOR,
            'SWATCH_TYPE_TEXTUAL'      => Swatch::SWATCH_TYPE_TEXTUAL,
            'productId'                => $product->getId(),
            'selectors'                => [],
            'options'                  => $this->prepareJsonOptions($options),
            'option'                   => [
                'CHOICE_METHOD_TOGGLE' => ChoiceMethod::TOGGLE,
                'CHOICE_METHOD_SELECT' => ChoiceMethod::SELECT,
                'choiceMethod'         => $this->configHelper->getSwatchChoiceMethod(),
                'showPrice'            => $this->configHelper->getSwatchShowPrice(),
                'selectors'            => [],
                'slider'               => [
                    'enabled'          => $this->configHelper->isSliderEnabled(),
                    'swatchesPerSlide' => $this->configHelper->getSwatchesPerSlide(),
                    'arrowsColor'      => $this->configHelper->getSliderArrowsColor(),
                ],
            ],
        ];
    }

    private function prepareJsonOptions(array $options)
    {
        $jsonOptions = [];
        foreach ($options as $option) {
            $jsonOptions[] = [
                'id'       => (int) $option->getId(),
                'multiple' => $this->isMultiple($option),
                'tooltip'  => $this->hasTooltip($option),
                'values'   => $this->prepareJsonOptionValues($option),
            ];
        }
        return $jsonOptions;
    }

    private function prepareJsonOptionValues(Option $option)
    {
        $jsonValues = [];
        /** @var \Magento\Catalog\Model\Product\Option\Value $value */

        foreach ((array) $option->getValues() as $value) {
            $jsonValues[] = [
                'id'    => (int) $value->getId(),
                'title' => $this->getSwatchFinalTitle($value),
            ];
        }
        return $jsonValues;
    }
}
