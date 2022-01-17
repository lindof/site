<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer;

use Magento\Framework\View\Element\Html\Select;

class Ddsmultiselect extends Select
{

    public function setInputName($inputName)
    {
        $this->setData('inputname', $inputName . '[]');

        return $this;
    }

    public function setColumnName($columnName)
    {
        $this->setData('columnname', $columnName);

        return $this;
    }

    public function setColumn($column)
    {
        $this->setData('column', $column);

        return $this;
    }

    public function getHtml()
    {
        return $this->toHtml();
    }

    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<select name="' . $this->getInputName() . '" class="' . $this->getClass() . '" ' . $this->getExtraParams() . ' multiple="multiple">';

        $values = $this->getValue();

        if (!is_array($values)) {
            if (!is_null($values)) {
                $values = [$values];
            } else {
                $values = [];
            }
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = $option['label'];
                $params = (!empty($option['params'])) ? $option['params'] : [];
            } else {
                $value = $key;
                $label = $option;
                $isArrayOption = false;
                $params = [];
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $label . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = [
                            'value' => $keyGroup,
                            'label' => $optionGroup,
                        ];
                    }
                    $html .= $this->_optionToHtml(
                        $optionGroup,
                        in_array($optionGroup['value'], $values, true)
                    );
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml([
                    'value' => $value,
                    'label' => $label,
                    'params' => $params,
                ], in_array($value, $values, true));
            }
        }
        $html .= '</select>';

        return $html;
    }

    public function getInputName()
    {
        return $this->getData('inputname');
    }

    public function getExtraParams()
    {
        $column = $this->getColumn();
        if ($column && isset($column['style'])) {
            return ' style="' . $column['style'] . '" ';
        }

        return '';
    }

    public function getColumn()
    {
        return $this->getData('column');
    }

    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';

        if ($this->getIsRenderToJsTemplate() === true) {
            //$selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
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

        return sprintf('<option value="%s"%s %s>%s</option>', $this->escapeHtml($option['value']), $selectedHtml, $params, $this->escapeHtml($option['label']));
    }

    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getColumnName() . $this->getInputName() . $optionValue));
    }

    public function getColumnName()
    {
        return $this->getData('columnname');
    }
}
