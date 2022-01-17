<?php


namespace Magesales\Converge\Gateway\Request\Builder\CreditCard;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;

/**
 * Class Complete
 */
class Complete implements BuilderInterface
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
     * Complete constructor.
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
            'ssl_transaction_type' => 'cccomplete',
            'ssl_txn_id' => $this->getTransaction($payment)->getTxnId(),
            'ssl_amount' => $this->subjectReader->readAmount($buildSubject)
        ];
    }

    /**
     * @param InfoInterface $payment
     * @return \Magento\Sales\Model\Order\Payment\Transaction
     * @throws LocalizedException
     */
    private function getTransaction(InfoInterface $payment)
    {
        $transaction = $this->transactionManager->getAuthorizationTransaction(
            null,
            $payment->getId(),
            $payment->getOrder()->getId()
        );

        if (!$transaction) {
            $transaction = $payment->getAuthorizationTransaction();
        }

        if (!$transaction instanceof TransactionInterface) {
            throw new LocalizedException(__('Authorization Transaction does not exist.'));
        }

        return $transaction;
    }
}
