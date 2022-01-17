<?php


namespace Magesales\Converge\Gateway\Response\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;

use Magesales\Converge\Gateway\Config\Config;
use Magesales\ConvergePaymentApi\Config\TransactionResponseFields;
use Magesales\Converge\Gateway\Helper\SubjectReader;

use Magento\Sales\Model\Order\Payment;

/**
 * Class UpdateCompletePaymentHandler.
 */
class UpdateCompletePaymentHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $additionalInformationMapping = [];

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * UpdateCompletePaymentHandler constructor.
     * @param SubjectReader $subjectReader
     * @param ContextHelper $contextHelper
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        ContextHelper $contextHelper,
        Config $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->contextHelper = $contextHelper;
        $this->config = $config;
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

        if ($this->config->getMode() === Config::MODE_DEMO) {
            $payment->setTransactionId($response[TransactionResponseFields::SSL_TXN_ID] . '-capture');
        } else {
            $payment->setTransactionId($response[TransactionResponseFields::SSL_TXN_ID]);
        }

        foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
            if (isset($response[$responseKey])) {
                $payment->setAdditionalInformation($informationKey, $response[$responseKey]);
            }
        }
    }
}
