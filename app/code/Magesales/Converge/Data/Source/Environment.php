<?php


namespace Magesales\Converge\Data\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Environment
 */
class Environment implements ArrayInterface
{
    const PRODUCTION = 'production';
    const DEMO = 'demo';

    /**
     * @return array - Returns an array of the available options
     */
    public function toOptionArray()
    {
        return [
            ["value" => self::PRODUCTION, "label" => __("Production")],
            ["value" => self::DEMO, "label" => __("Demo")]
        ];
    }
}
