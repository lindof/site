<?php

namespace Cminds\Marketplace\Block\Plugin\Block\Adminhtml\Widget\Grid\Column\Filter;

class Price
{
    public function beforeGetCondition(\Magento\Backend\Block\Widget\Grid\Column\Filter\Price $subject)
    {
        $value = $subject->getValue();

        if (isset($value['from'])) {
            $value['from'] = (float) $value['from'];
        }

        if (isset($value['to'])) {
            $value['to'] = (float) $value['to'];
        }

        $subject->setData('value', $value);
    }
}
