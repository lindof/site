<?php


namespace Magesales\ConvergePaymentSpi;

/**
 * Interface Transaction.
 * @api
 */
interface Transaction
{
    /**
     * Execute transaction.
     *
     * @param array $request
     * @return array
     */
    public function execute(array $request);
}
