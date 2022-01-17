<?php


namespace Magesales\ConvergePaymentSpi;

/**
 * Interface TransactionService.
 * @api
 */
interface TransactionService
{
    /**
     * @param string $transactionName
     * @param array $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function execute($transactionName, array $request);
}
