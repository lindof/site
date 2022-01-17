<?php

namespace Orange35\Colorpickercustom\Block\Product\Renderer;

use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Model\Swatch;
use Orange35\Colorpickercustom\Helper\ConfigHelper;
use Orange35\Colorpickercustom\Helper\OptionHelper;
use Orange35\Colorpickercustom\Model\HtmlElementTrait;

/**
 * Class Swatches - render swatches for given Option
 * @package Orange35\Colorpickercustom\Block\Product\Renderer
 */
class Swatches extends \Magento\Framework\View\Element\Template
{
    use HtmlElementTrait;

    private $optionHelper;
    private $attribs = ['class' => 'swatch-attribute-options o35-swatches clearfix'];
    private $configHelper;

    public function __construct(
        Template\Context $context,
        OptionHelper $optionHelper,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->optionHelper = $optionHelper;
    }

    public function getAttribs()
    {
        $attribs = $this->attribs;
        if ($this->configHelper->isSliderEnabled()) {
            // show only one line of swatches to avoid content jumping after slider initialization
            $height = $this->optionHelper->getSwatchFinalHeight($this->getOption())
                + 10 // swatch margin
                + 2 * (1 + 1) /* padding & border from Magento_Swatches::css/swatches.css */
            ;
            $attribs['style'] = 'overflow-y:hidden; height: ' . $height . 'px';
        }
        return $attribs;
    }

    public function getAttrib($name)
    {
        return array_key_exists($name, $this->attribs) ? $this->attribs[$name] : null;
    }

    public function setAttrib($name, $value)
    {
        $this->attribs[$name] = $value;
    }

    public function addClass($class)
    {
        $classes = array_filter(explode(' ', '' . $this->getAttrib('class')), 'trim');
        if (!in_array($class, $classes)) {
            $classes[] = $class;
            $this->setAttrib('class', implode(' ', $classes));
        }
        return $this;
    }

    /**
     * @return Option
     */
    public function getOption()
    {
        return $this->getData('option');
    }

    //phpcs:ignore MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function _toHtml()
    {
        $swatchesHtml = implode('', array_map([$this, 'renderSwatch'], (array) $this->getOption()->getValues()));
        return $this->el('div', $this->getAttribs(), $swatchesHtml);
    }

    private function renderSwatch(Option\Value $value)
    {
        $text = Swatch::SWATCH_TYPE_TEXTUAL === $this->optionHelper->getSwatchType($value)
            ? $value->getTitle()
            : '&nbsp;';

        $attributes = [
            'data-role'    => 'o35-swatch',
            'data-id'      => $value->getId(),
            'option-type'  => $this->optionHelper->getSwatchFinalType($value),
            'option-label' => $this->optionHelper->getSwatchFinalTitle($value),
            'class'        => 'swatch-option',
            'style'        => $this->optionHelper->getSwatchCss($value),
        ];
        switch ($this->optionHelper->getSwatchType($value)) {
            case Swatch::SWATCH_TYPE_TEXTUAL:
                $attributes['class'] .= ' textual';
                break;
            case Swatch::SWATCH_TYPE_VISUAL_COLOR:
                $attributes['class'] .= ' color';
                $color = $this->optionHelper->getSwatchColor($value);
                $attributes['style'] .= 'background: ' . $color . ' no-repeat center; background-size: initial;';
                if ($this->optionHelper->hasTooltip($value->getOption())) {
                    $attributes['option-tooltip-value'] = $color;
                }
                break;
            case Swatch::SWATCH_TYPE_VISUAL_IMAGE:
                $attributes['class'] .= ' image';
                $url = $this->optionHelper->getSwatchImageUrl($value);
                $attributes['style'] .= "background: url('$url') no-repeat center; background-size: initial;";
                if ($this->optionHelper->hasTooltip($value->getOption())) {
                    $attributes['option-tooltip-thumb'] = $this->optionHelper->getSwatchTooltipImageUrl($value);
                }
                break;
        }
        return $this->el('div', $attributes, $text);
    }
}
