<?php


namespace Magesales\Converge\Gateway\Response\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;

use Magesales\ConvergePaymentApi\Config\TransactionResponseFields;
use Magesales\Converge\Gateway\Helper\SubjectReader;

use Magento\Sales\Model\Order\Payment;

/**
 * Class UpdatePaymentHandler.
 */
class UpdatePaymentHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $additionalInformationMapping = [
        'approval_code' => TransactionResponseFields::SSL_APPROVAL_CODE,
        'approval_message' => TransactionResponseFields::SSL_APPROVAL_MESSAGE,
        'card_type' => TransactionResponseFields::SSL_CARD_SHORT_DESCRIPTION,
        'avs_response' => TransactionResponseFields::SSL_AVS_RESPONSE,
        'cvv2_response' => TransactionResponseFields::SSL_CVV2_RESPONSE,
        'invoice_number' => TransactionResponseFields::SSL_INVOICE_NUMBER,
        'departure_date' => TransactionResponseFields::SSL_DEPARTURE_DATE,
        'completion_date' => TransactionResponseFields::SSL_COMPLETION_DATE,
    ];

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * UpdatePaymentHandler constructor.
     * @param SubjectReader $subjectReader
     * @param ContextHelper $contextHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        ContextHelper $contextHelper
    ) {
        $this->subjectReader = $subjectReader;
        $this->contextHelper = $contextHelper;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $commandSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $this->contextHelper->assertOrderPayment($payment);

        $payment->setTransactionId($response[TransactionResponseFields::SSL_TXN_ID]);

        foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
            if (isset($response[$responseKey])) {
                $payment->setAdditionalInformation($informationKey, $response[$responseKey]);
            }
        }
    }
}
