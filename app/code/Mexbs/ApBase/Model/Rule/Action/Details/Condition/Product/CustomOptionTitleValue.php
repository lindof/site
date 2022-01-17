<?php
namespace Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product;

class CustomOptionTitleValue extends \Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product
{
    protected $productConfiguration;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        array $data = []
    ) {
        $this->productConfiguration = $productConfiguration;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    public function getOptionTitleElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__option_title',
            'text',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getSubPrefix() . '][' . $this->getId() . '][option_title]',
                'value' => $this->getOptionTitle(),
                'value_name' => ($this->getOptionTitle() ? $this->getOptionTitle() : "..."),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Editable')
            );
    }

    public function getOptionValueElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__option_value',
            'text',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getSubPrefix() . '][' . $this->getId() . '][option_value]',
                'value' => $this->getOptionValue(),
                'value_name' => ($this->getOptionValue() ? $this->getOptionValue() : "..."),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Editable')
            );
    }

    public function getOptionValueOperatorElement()
    {
        $options = $this->getOperatorSelectOptions();
        if ($this->getOptionValueOperator() === null) {
            foreach ($options as $option) {
                $this->setOptionValueOperator($option['value']);
                break;
            }
        }

        $elementId = sprintf('%s_%s__%s__option_value_operator', $this->getSubPrefix(), $this->getPrefix(), $this->getId());
        $elementName = sprintf($this->elementName . '[%s][%s][%s][option_value_operator]', $this->getPrefix(), $this->getSubPrefix(), $this->getId());
        $element = $this->getForm()->addField(
            $elementId,
            'select',
            [
                'name' => $elementName,
                'values' => $options,
                'value' => $this->getOptionValueOperator(),
                'value_name' => $this->getOptionValueOperatorName(),
                'data-form-part' => $this->getFormName()
            ]
        );
        $element->setRenderer($this->_layout->getBlockSingleton('Magento\Rule\Block\Editable'));

        return $element;
    }

    public function isArrayOperatorTypeByOperator($operator)
    {
        return $operator === '()' || $operator === '!()' || in_array($this->getInputType(), $this->_arrayInputTypes);
    }


    protected function _getComparisonResult($value, $validatedValue, $operator){
        $result = false;

        switch ($operator) {
            case '==':
            case '!=':
                if (is_array($value)) {
                    if (is_array($validatedValue)) {
                        $result = array_intersect($value, $validatedValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($validatedValue)) {
                        $result = count($validatedValue) == 1 && array_shift($validatedValue) == $value;
                    } else {
                        $result = $this->_compareValues($validatedValue, $value);
                    }
                }
                break;

            case '<=':
            case '>':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = $validatedValue <= $value;
                }
                break;

            case '>=':
            case '<':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = $validatedValue >= $value;
                }
                break;

            case '{}':
            case '!{}':
                if (is_scalar($validatedValue) && is_array($value)) {
                    foreach ($value as $item) {
                        if (stripos($validatedValue, (string)$item) !== false) {
                            $result = true;
                            break;
                        }
                    }
                } elseif (is_array($value)) {
                    if (is_array($validatedValue)) {
                        $result = array_intersect($value, $validatedValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($validatedValue)) {
                        $result = in_array($value, $validatedValue);
                    } else {
                        $result = $this->_compareValues($value, $validatedValue, false);
                    }
                }
                break;

            case '()':
            case '!()':
                if (is_array($validatedValue)) {
                    $result = count(array_intersect($validatedValue, (array)$value)) > 0;
                } else {
                    $value = (array)$value;
                    foreach ($value as $item) {
                        if ($this->_compareValues($validatedValue, $item)) {
                            $result = true;
                            break;
                        }
                    }
                }
                break;
        }

        if ('!=' == $operator || '>' == $operator || '<' == $operator || '!{}' == $operator || '!()' == $operator) {
            $result = !$result;
        }

        return $result;
    }

    public function isArrayOperatorType()
    {
        $operator = $this->getOptionValueOperator();
        return $operator === '()' || $operator === '!()' || in_array($this->getInputType(), $this->_arrayInputTypes);
    }

    public function validate(\Magento\Framework\Model\AbstractModel $item)
    {
        $customOptionRuleTitle = $this->getOptionTitle();

        $customOptionRuleValue = $this->getOptionValue();
        $customOptionRuleValueOperator = $this->getOptionValueOperator();

        if (!is_array($customOptionRuleValue) && $this->isArrayOperatorType() && $customOptionRuleValue) {
            $customOptionRuleValue = preg_split('#\s*[,;]\s*#', $customOptionRuleValue, null, PREG_SPLIT_NO_EMPTY);
        }

        if ($this->isArrayOperatorTypeByOperator($customOptionRuleValueOperator)
            xor is_array($customOptionRuleValue)) {
            return false;
        }

        $itemCustomOptions = $this->productConfiguration->getCustomOptions($item);


        foreach($itemCustomOptions as $itemCustomOption){
            if(isset($itemCustomOption['label'])
                && ($customOptionRuleTitle == $itemCustomOption['label'])){
                if(!isset($itemCustomOption['value'])){
                    return false;
                }
                return $this->_getComparisonResult(
                    $customOptionRuleValue,
                    $itemCustomOption['value'],
                    $customOptionRuleValueOperator
                );
            }
        }

        return false;
    }

    public function getOptionTitleElementHtml()
    {
        return $this->getOptionTitleElement()->getHtml();
    }

    public function getOptionValueElementHtml()
    {
        return $this->getOptionValueElement()->getHtml();
    }

    public function getOptionTitleOperatorName()
    {
        return $this->getOperatorOption($this->getOptionTitleOperator());
    }

    public function getOptionValueOperatorName()
    {
        return $this->getOperatorOption($this->getOptionValueOperator());
    }


    public function getOptionTitleOperatorElementHtml()
    {
        return $this->getOptionTitleOperatorElement()->getHtml();
    }

    public function getOptionValueOperatorElementHtml()
    {
        return $this->getOptionValueOperatorElement()->getHtml();
    }

    public function asHtml()
    {
        $html = $this->getTypeElementHtml();

        $html .= __(
            "If the value of custom option with title %1 %2 %3",
            $this->getOptionTitleElementHtml(),
            $this->getOptionValueOperatorElementHtml(),
            $this->getOptionValueElementHtml()
        );

        $html .= $this->getRemoveLinkHtml() .
            $this->getChooserContainerHtml();
        return $html;
    }

    public function loadArray($arr)
    {
        $this->setType($arr['type']);
        $this->setOptionTitle(isset($arr['option_title']) ? $arr['option_title'] : null);
        $this->setOptionValue(isset($arr['option_value']) ? $arr['option_value'] : null);
        $this->setOptionValueOperator(isset($arr['option_value_operator']) ? $arr['option_value_operator'] : null);
    }

    public function asArray(array $arrAttributes = [])
    {
        $out = [
            'type' => $this->getType(),
            'option_title' => $this->getOptionTitle(),
            'option_value' => $this->getOptionValue(),
            'option_value_operator' => $this->getOptionValueOperator()
        ];
        return $out;
    }
	
	public function getAttribute(): string
    {
        return "custom_option_title_value";
    }

    public function collectValidatedAttributes($productCollection)
    {
        return $this;
    }
}