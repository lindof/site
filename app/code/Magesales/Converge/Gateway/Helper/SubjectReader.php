<?php


namespace Magesales\Converge\Gateway\Helper;

use Magento\Payment\Gateway\Helper;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Class SubjectReader
 */
class SubjectReader
{
    /**
     * Reads response object from subject.
     *
     * @param array $subject
     * @return array
     */
    public function readResponseObject(array $subject)
    {
        return Helper\SubjectReader::readResponse($subject);
    }

    /**
     * @param array $subject
     * @return \Magento\Framework\DataObject
     */
    public function readStateObject(array $subject)
    {
        return Helper\SubjectReader::readStateObject($subject);
    }

    /**
     * Reads payment from subject.
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads amount from subject.
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }
}
