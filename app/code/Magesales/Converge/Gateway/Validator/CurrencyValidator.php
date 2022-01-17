<?php


namespace Magesales\Converge\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magesales\ConvergePaymentApi\Config;

/**
 * Class CurrencyValidator.
 */
class CurrencyValidator extends AbstractValidator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Currency constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param Config $config
     */
    public function __construct(ResultInterfaceFactory $resultFactory, Config $config)
    {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return bool
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['currency'])) {
            return $this->createResult(true);
        }
        $currencyCode = $validationSubject['currency'];

        $allowedCurrencies = explode(',', $this->config->getValue('allowed_currencies'));

        if (!$currencyCode || !in_array($currencyCode, $allowedCurrencies)) {
            return $this->createResult(false);
        }

        return $this->createResult(true);
    }
}
