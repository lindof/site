<?php


namespace Magesales\Converge\Gateway\Http\Converter;

use Magento\Payment\Gateway\Http\ConverterInterface;

/**
 * Class UriStringToArray
 */
class UriStringToArray implements ConverterInterface
{
    /**
     * Converts gateway response to array structure.
     *
     * @param string $response
     * @return array
     */
    public function convert($response)
    {
        if (strpos($response, 'HTML') !== false) {
            return $response;
        }
        $response = explode("\n", $response);
        $modifiedResponse = [];
        foreach ($response as $value) {
            if (strpos($value, '=') !== false) {
                list($key, $value) = explode('=', $value);
                $modifiedResponse[$key] = $value;
            }
        }
        $response = (array) $modifiedResponse;
        foreach ($response as $key => $value) {
            if (is_object($value)) {
                $response[$key] = $this->convert($value);
            }
        }

        return $response;
    }
}
