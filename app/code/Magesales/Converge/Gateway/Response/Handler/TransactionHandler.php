<?php


namespace Magesales\Converge\Gateway\Response\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;

/**
 * Class TransactionHandler
 */
class TransactionHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * UpdatePaymentHandler constructor.
     * @param SubjectReader $reader
     */
    public function __construct(
        SubjectReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $commandSubject, array $response)
    {
        $paymentDataObject = $this->reader->readPayment($commandSubject);

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDataObject->getPayment();

        foreach ($response as $key => $value) {
            if (!is_object($value)) {
                $payment->setTransactionAdditionalInfo($key, $value);
            }
        }
        $payment->setTransactionAdditionalInfo(
            'raw_details_info',
            $payment->getTransactionAdditionalInfo()
        );
    }
}
