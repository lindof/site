<?php


namespace Magesales\Converge\Gateway\Validator;

use Magento\Framework\Exception\NotFoundException;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

use Magesales\Converge\Gateway\Config\Config;

/**
 * Class GlobalValidator.
 */
class GlobalValidator extends AbstractValidator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * GlobalValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param Config $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function validate(array $validationSubject)
    {
        return $this->createResult(true);
    }
}
