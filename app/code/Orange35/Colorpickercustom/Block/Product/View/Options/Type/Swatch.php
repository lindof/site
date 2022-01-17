<?php

namespace Orange35\Colorpickercustom\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Orange35\Colorpickercustom\Helper\ConfigHelper;
use Orange35\Colorpickercustom\Helper\OptionHelper;
use Orange35\Colorpickercustom\Block\Product\Renderer\Swatches as SwatchesRenderer;
use Orange35\Colorpickercustom\Model\HtmlElementTrait;

/**
 * Class Swatch
 * @package Orange35\Colorpickercustom\Block\Product\View\Options\Type
 * @method getProduct()
 * @method \Magento\Catalog\Model\Product\Option getOption()
 */
class Swatch extends Template
{
    use HtmlElementTrait;

    private $imageHelper;
    private $configHelper;
    private $optionHelper;
    private $swatchesRenderer;

    /** @var Select */
    private $select;

    public function __construct(
        Context $context,
        OptionHelper $optionHelper,
        ConfigHelper $configHelper,
        SwatchesRenderer $swatchesRenderer,
        Select $select,
        array $data = []
    ) {
        $this->select = $select;
        $this->configHelper = $configHelper;
        $this->optionHelper = $optionHelper;
        $this->swatchesRenderer = $swatchesRenderer;
        parent::__construct($context, $data);
    }

    public function getValuesHtml()
    {
        $option = $this->getOption();
        $swatches = $this->swatchesRenderer->setData('option', $option)->toHtml();
        $this->select->setProduct($this->getProduct())->setOption($this->getOption());
        $html = $this->select->getValuesHtml();

        // add styles required to hide element but show error message when element is required
        $html = str_replace(
            '<select ',
            '<select style="position:absolute; border:0; height: 0; padding: 0; margin: 0; visibility: hidden;" ',
            $html
        );

        $html = $this->el(
            'div',
            [
                'class'     => 'field' . ($option->getIsRequire() ? ' required' : '') . ' o35-option',
                'data-role' => 'o35-option',
                'data-id'   => $option->getId(),
            ],
            $this->el('span', ['class' => 'label swatch-attribute-label'], $option->getTitle())
            . $this->el('span', ['class' => 'swatch-attribute-selected-option'], '')
            . $swatches
            . $html
        );

        return $html;
    }
}
