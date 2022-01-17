<?php


namespace Magesales\Converge\Gateway\Command;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magesales\Converge\Gateway\Helper\SubjectReader;
use Psr\Log\LoggerInterface;

/**
 * Class GatewayCommand
 */
class GatewayCommand implements CommandInterface
{
    /**
     * @var HandlerInterface
     */
    private $responseHandler;

    /**
     * @var ArrayResultFactory
     */
    private $arrayResultFactory;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GatewayCommand constructor
     *
     * @param SubjectReader $reader
     * @param ArrayResultFactory $arrayResultFactory
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param HandlerInterface|null $responseHandler
     */
    public function __construct(
        SubjectReader $reader,
        ArrayResultFactory $arrayResultFactory,
        LoggerInterface $logger,
        ValidatorInterface $validator = null,
        HandlerInterface $responseHandler = null
    ) {
        $this->reader = $reader;
        $this->responseHandler = $responseHandler;
        $this->arrayResultFactory = $arrayResultFactory;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $response = $this->reader->readResponseObject($commandSubject);

        if ($this->validator) {
            //Store validation errors
            $validationResult = $this->validator->validate(
                ['response' => $response]
            );

            if (!$validationResult->isValid()) {
                $this->logMessages($validationResult->getFailsDescription());
                throw new LocalizedException(__('Transaction has not been processed. Please try again later.'));
            }
        }

        if ($this->responseHandler) {
            $this->responseHandler->handle(
                $commandSubject,
                $response
            );
        }

        return null;
    }

    /**
     * @param array $messages
     */
    private function logMessages(array $messages)
    {
        foreach ($messages as $failMessage) {
            $this->logger->critical((string) $failMessage);
        }
    }
}
