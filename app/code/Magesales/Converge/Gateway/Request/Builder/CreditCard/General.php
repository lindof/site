<?php


namespace Magesales\Converge\Gateway\Request\Builder\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magesales\Converge\Gateway\Config\Config;
use Magesales\ConvergePaymentApi\Config\TransactionFields;

/**
 * Class General
 */
class General implements BuilderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * General constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $testMode =  $this->config->getMode() === Config::MODE_DEMO ? 'true' : 'false';

        return [
            TransactionFields::SSL_MERCHANT_ID => $this->config->getMerchantId(),
            TransactionFields::SSL_USER_ID => $this->config->getUserId(),
            TransactionFields::SSL_PIN => $this->config->getPin(),
            TransactionFields::SSL_RESULT_FORMAT => 'ASCII',
            TransactionFields::SSL_TEST_MODE => $testMode
        ];
    }
}
