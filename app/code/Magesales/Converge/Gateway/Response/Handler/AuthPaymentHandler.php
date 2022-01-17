<?php


namespace Magesales\Converge\Gateway\Response\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magesales\ConvergePaymentApi\Config;
use Psr\Log\LoggerInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;

/**
 * Class AuthPaymentHandler.
 */
class AuthPaymentHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * CaptureHandler constructor
     *
     * @param SubjectReader $reader
     * @param OrderSender $orderSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        SubjectReader $reader,
        OrderRepositoryInterface $orderRepository,
        OrderSender $orderSender,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->reader = $reader;
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDataObject = $this->reader->readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDataObject->getPayment();

        if ($this->config->getPaymentAction() === 'authorize') {
            $payment->setShouldCloseParentTransaction(false);
            $payment->setIsTransactionClosed(false);
        } else {
            $payment->setIsTransactionClosed(true);
        }

        $this->orderRepository->save($payment->getOrder());
    }
}
