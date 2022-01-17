<?php


namespace Magesales\Converge\Gateway\Request\Builder\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;

/**
 * Class CcReturn
 */
class CcReturn implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ManagerInterface
     */
    private $transactionManager;

    /**
     * CcReturn constructor.
     * @param SubjectReader $subjectReader
     * @param ManagerInterface $transactionManager
     */
    public function __construct(
        SubjectReader $subjectReader,
        ManagerInterface $transactionManager
    ) {
        $this->subjectReader = $subjectReader;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        return [
            'ssl_transaction_type' => 'ccreturn',
            'ssl_txn_id' => $payment->getRefundTransactionId(),
            'ssl_amount' => $this->subjectReader->readAmount($buildSubject)
        ];
    }
}
