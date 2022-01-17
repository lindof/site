<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer;

use Magento\Framework\View\Element\Html\Select as MagentoSelect;

class Ddselect extends MagentoSelect
{

    /**
     * Options.
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Add an option to HTML select.
     *
     * @param string $value HTML value
     * @param string $label HTML label
     * @param array $params HTML attributes
     *
     * @return $this
     */
    public function addOption($value, $label, $params = [])
    {
        $this->_options[] = ['value' => $value, 'label' => $label, 'params' => $params];

        return $this;
    }

    /**
     * Set element's HTML ID.
     *
     * @param string $elementId ID
     *
     * @return $this
     */
    public function setId($elementId)
    {
        $this->setData('id', $elementId);

        return $this;
    }

    /**
     * Set element's CSS class.
     *
     * @param string $class Class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->setData('class', $class);

        return $this;
    }

    /**
     * Set element's HTML title.
     *
     * @param string $title Title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);

        return $this;
    }

    public function setInputName($inputName)
    {
        $this->setData('inputname', $inputName);

        return $this;
    }

    /**
     * Alias for toHtml().
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }

    /**
     * Render HTML.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        $style = 'style=width:100px';
        if ($this->getStyle()) {
            $style = 'style="' . $this->getStyle() . '"';
        }
        if (strchr($this->getInputName(), "order_type")) {
            $html = '<select name="' .
                $this->getInputName() .
                '" class="' .
                $this->getClass() .
                '" title="' .
                $this->getTitle() .
                '" ' .
                $this->getExtraParams() . 'style=width:108px;' .
                '>';
        } else {
            $html = '<select name="' .
                $this->getInputName() .
                '" class="' .
                $this->getClass() .
                '" title="' .
                $this->getTitle() .
                '" ' .
                $this->getExtraParams() . $style .
                '>';
        }

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = (array)$values;
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            $optgroupName = '';
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = (string)$option['label'];
                $optgroupName = isset($option['optgroup-name']) ? $option['optgroup-name'] : $label;
                $params = !empty($option['params']) ? $option['params'] : [];
            } else {
                $value = (string)$key;
                $label = (string)$option;
                $isArrayOption = false;
                $params = [];
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $label . '" data-optgroup-name="' . $optgroupName . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = ['value' => $keyGroup, 'label' => $optionGroup];
                    }
                    $html .= $this->_optionToHtml($optionGroup, in_array($optionGroup['value'], $values, true));
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml(
                    ['value' => $value, 'label' => $label, 'params' => $params],
                    in_array($value, $values, true)
                );
            }
        }
        $html .= '</select>';

        return $html;
    }

    public function getInputName()
    {
        return $this->getData('inputname');
    }

    /**
     * CSS class of the element.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Get options of the element.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options for the HTML select.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->_options = $options;

        return $this;
    }

    /**
     * Return option HTML node.
     *
     * @param array $option
     * @param bool $selected
     *
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' <%= option_extra_attrs.option_' . self::calcOptionHash($option['value']) . ' %>';
        }

        $params = '';
        if (!empty($option['params']) && is_array($option['params'])) {
            foreach ($option['params'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyMulti => $valueMulti) {
                        $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                    }
                } else {
                    $params .= sprintf(' %s="%s" ', $key, $value);
                }
            }
        }

        return sprintf(
            '<option value="%s"%s %s>%s</option>',
            $this->escapeHtml($option['value']),
            $selectedHtml,
            $params,
            $this->escapeHtml($option['label'])
        );
    }

    /**
     * Calculate CRC32 hash for option value.
     *
     * @param string $optionValue Value of the option
     *
     * @return string
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getInputName() . $this->getId() . $optionValue));
    }

    /**
     * HTML ID of the element.
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }
}
