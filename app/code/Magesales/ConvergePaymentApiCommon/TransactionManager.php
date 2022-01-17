<?php


namespace Magesales\ConvergePaymentApiCommon\Transaction;

use Magesales\ConvergePaymentApi\ValidationException;
use Magesales\ConvergePaymentApi\Config\TransactionFields;

use Magesales\ConvergePaymentSpi\TransactionService;
use Magesales\ConvergePaymentSpi\ValidationService;

/**
 * Class TransactionManager.
 * @private
 */
class TransactionManager
{
    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var ValidationService
     */
    private $validationService;

    /**
     * TransactionManager constructor.
     *
     * @param TransactionService $transactionService
     * @param ValidationService $validationService
     */
    public function __construct(
        TransactionService $transactionService,
        ValidationService $validationService
    ) {
        $this->transactionService = $transactionService;
        $this->validationService = $validationService;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $request)
    {
        if (!isset($request[TransactionFields::SSL_TRANSACTION_TYPE])) {
            throw new ValidationException("Missed Transaction Type in response.");
        }
        $transactionName = $request[TransactionFields::SSL_TRANSACTION_TYPE];

        $response = $this->transactionService->execute($transactionName, $request);

        try {
            $this->validationService->validate($request, $response);
        } catch (\Exception $e) {
            throw new ValidationException($e->getMessage(), $e->getCode());
        }

        return $response;
    }
}
