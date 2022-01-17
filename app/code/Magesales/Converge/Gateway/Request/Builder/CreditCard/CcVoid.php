<?php


namespace Magesales\Converge\Gateway\Request\Builder\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;

/**
 * Class CcVoid
 */
class CcVoid implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CcVoid constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $payment = $this->subjectReader->readPayment($buildSubject)->getPayment();

        return [
            'ssl_transaction_type' => 'ccvoid',
            'ssl_txn_id' => $payment->getLastTransId()
        ];
    }
}
