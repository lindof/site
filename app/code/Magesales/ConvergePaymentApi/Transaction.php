<?php


namespace Magesales\ConvergePaymentApi;

/**
 * Interface Transaction.
 */
interface Transaction
{
    /**
     * @param array $request
     * @return mixed
     */
    public function execute(array $request);
}
