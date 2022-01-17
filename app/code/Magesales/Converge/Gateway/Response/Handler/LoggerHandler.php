<?php


namespace Magesales\Converge\Gateway\Response\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class LoggerHandler
 */
class LoggerHandler implements HandlerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * LoggerHandler constructor.
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function handle(array $commandSubject, array $response)
    {
        $this->logger->debug($response);
    }
}
