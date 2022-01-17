<?php


namespace Magesales\ConvergePaymentSpi;

/**
 * Interface ValidationService.
 * @api
 */
interface ValidationService
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
