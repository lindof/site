<?php


namespace Magesales\Converge\Ui\Block;

use Magento\Payment\Block\ConfigurableInfo;

/**
 * Class Info.
 */
class Info extends ConfigurableInfo
{
    /**
     * @param string $field
     * @return string
     */
    protected function getLabel($field)
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}
