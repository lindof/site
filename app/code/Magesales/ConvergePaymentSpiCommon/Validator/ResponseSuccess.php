<?php


namespace Magesales\ConvergePaymentSpiCommon\Validator;

use Magesales\ConvergePaymentApi\ValidationException;
use Magesales\ConvergePaymentSpi\ValidatorInterface;

/**
 * Class ResponseSuccess
 */
class ResponseSuccess implements ValidatorInterface
{
    /**
     * @param array $request
     * @param array $response
     * @return bool
     * @throws ValidationException
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function validate(array $request, array $response)
    {
        $errorCode = null;
        $errorMessage = '';

        if (isset($response['errorCode']) && $response['errorCode'] != 0) {
            $errorCode = $response['errorCode'];
            $errorMessage = $response['errorMessage'];
        }

        if (isset($response['ssl_result']) && $response['ssl_result'] != 0) {
            $errorCode = $response['ssl_result'];
            $errorMessage = $response['ssl_result_message'];
        }

        if ($errorCode) {
            throw new ValidationException($errorMessage, $errorCode);
        }

        return true;
    }
}
