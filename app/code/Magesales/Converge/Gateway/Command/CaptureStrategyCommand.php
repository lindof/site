<?php


namespace Magesales\Converge\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;
use Magesales\ConvergePaymentApi\Config;

/**
 * Class CaptureStrategyCommand
 */
class CaptureStrategyCommand implements CommandInterface
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var Config
     */
    private $config;

    /**
     * CaptureStrategyCommand constructor.
     * @param CommandPoolInterface $commandPool
     * @param SubjectReader $reader
     * @param Config $config
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        SubjectReader $reader,
        Config $config
    ) {
        $this->commandPool = $commandPool;
        $this->reader = $reader;
        $this->config = $config;
    }

    /**
     * @param array $commandSubject
     * @return ResultInterface
     */
    public function execute(array $commandSubject)
    {
        $paymentDataObject = $this->reader->readPayment($commandSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDataObject->getPayment();

        if ($payment->getAuthorizationTransaction()) {
            $command = $this->commandPool->get('complete');
        } else {
            $command = $this->commandPool->get('authorize');
        }

        return $command->execute($commandSubject);
    }
}
