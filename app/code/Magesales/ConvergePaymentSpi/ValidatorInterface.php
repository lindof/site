<?php


namespace Magesales\ConvergePaymentSpi;

/**
 * Interface Validator.
 * @api
 */
interface ValidatorInterface
{
    /**
     * Validate result of transaction.
     *
     * @param array $request
     * @param array $response
     * @return bool true on success
     * @throws ValidatorException
     */
    public function validate(array $request, array $response);
}
