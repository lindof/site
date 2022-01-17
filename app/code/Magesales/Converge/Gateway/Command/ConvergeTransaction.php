<?php


namespace Magesales\Converge\Gateway\Command;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magesales\ConvergePaymentApi\Transaction;
use Magesales\ConvergePaymentApi\ValidationException;

/**
 * Class ConvergeTransaction.
 * This class has multiple DI Virtual Types and can't be used by direct reference.
 */
class ConvergeTransaction implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var Transaction
     */
    private $transactionApiCall;

    /**
     * @var HandlerInterface
     */
    private $responseHandler;

    /**
     * ConvergeTransaction constructor.
     *
     * @param BuilderInterface $requestBuilder
     * @param Transaction $transactionApiCall
     * @param HandlerInterface $responseHandler
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        Transaction $transactionApiCall,
        HandlerInterface $responseHandler = null
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->transactionApiCall = $transactionApiCall;
        $this->responseHandler = $responseHandler;
    }

    /**
     * @inheritDoc
     * @return null
     */
    public function execute(array $commandSubject)
    {
        try {
            $response = $this->transactionApiCall->execute(
                $this->requestBuilder->build($commandSubject)
            );
        } catch (ValidationException $exception) {
            throw new LocalizedException(__('Gateway Error: %1', $exception->getMessage()));
        } catch (\Exception $e) {
            throw new CommandException(
                __($e->getMessage()),
                $e
            );
        }

        if ($this->responseHandler) {
            $this->responseHandler->handle(
                $commandSubject,
                $response
            );
        }

        return null;
    }
}
