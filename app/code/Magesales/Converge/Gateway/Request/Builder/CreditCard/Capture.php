<?php


namespace Magesales\Converge\Gateway\Request\Builder\CreditCard;

use Magento\Framework\Escaper;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magesales\Converge\Data\Source\FormType;
use Magesales\Converge\Gateway\Helper\SubjectReader;
use Magesales\Converge\Gateway\Config\Config;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;
use Magento\Directory\Model\CountryInformationAcquirer;
use Magento\Payment\Model\Method\Logger;
use Magesales\Converge\Observer\DataAssignObserver;
use Magento\Payment\Model\InfoInterface;

/**
 * Class Capture
 */
class Capture implements BuilderInterface
{
    const MAX_AVS_ADDRESS_LENGTH = 30;
    const MAX_ADDRESS_LENGTH = 30;
    const MAX_CITY_LENGTH = 30;
    const MAX_COMPANY_LENGTH = 50;
    const MAX_COUNTRY_LENGTH = 3;
    const MAX_CUSTOMER_FIRST_NAME_LENGTH = 20;
    const MAX_CUSTOMER_LAST_NAME_LENGTH = 30;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArrayResultFactory
     */
    private $arrayResultFactory;

    /**
     * @var CountryInformationAcquirer
     */
    private $countryInformationAcquirer;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * RedirectCommand constructor.
     * @param SubjectReader $subjectReader
     * @param Config $config
     * @param ArrayResultFactory $arrayResultFactory
     * @param CountryInformationAcquirer $countryInformationAcquirer
     * @param Escaper $escaper
     * @param StoreManagerInterface $storeManager
     * @param Logger $logger
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config $config,
        ArrayResultFactory $arrayResultFactory,
        CountryInformationAcquirer $countryInformationAcquirer,
        Escaper $escaper,
        StoreManagerInterface $storeManager,
        Logger $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->arrayResultFactory = $arrayResultFactory;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $params['ssl_transaction_type'] = $this->config->getPaymentAction() == 'authorize' ? 'ccauthonly' : 'ccsale';
        $params['ssl_show_form'] = $this->config->getFormType() == FormType::MERCHANT ? 'false' : 'true';

        $paymentDataObject = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $order = $paymentDataObject->getOrder();

        $params['ssl_description'] = 'Transaction for Order #' . $order->getOrderIncrementId();
        $params['ssl_invoice_number'] = ($this->config->getMode() === Config::MODE_DEMO ? 'test-' : '')
            . $order->getOrderIncrementId();
        $params['ssl_customer_code'] = $order->getOrderIncrementId();

        $params['ssl_amount'] = $order->getGrandTotalAmount();
        $params['ssl_salestax'] = $payment->getOrder()->getBaseTaxAmount();

        $billingAddress = $order->getBillingAddress();
        if (null !== $billingAddress) {
            $this->setBillingAddressDetails($billingAddress, $params);
        }

        $shippingAddress = $order->getShippingAddress();
        if (null !== $shippingAddress) {
            $this->setShippingAddressDetails($shippingAddress, $params);
        }

        $params['ssl_card_number'] = $payment->getData(DataAssignObserver::CC_NUMBER);
        $params['ssl_exp_date'] = $this->getExpDate($payment);
        $params['ssl_cvv2cvc2'] = $payment->getData(DataAssignObserver::CC_CID);
        $params['ssl_cvv2cvc2_indicator'] = 1;

        return $params;
    }

    /**
     * @param AddressAdapterInterface $billingAddress
     * @param array $params
     */
    private function setBillingAddressDetails(AddressAdapterInterface $billingAddress, array &$params)
    {
        $params['ssl_company'] = (string)$billingAddress->getCompany();
        $params['ssl_first_name'] = $billingAddress->getFirstName();
        $params['ssl_last_name'] = $billingAddress->getLastname();
        $params['ssl_avs_address'] = $this->getSubstrMaxValue(
            $billingAddress->getStreetLine1(),
            self::MAX_AVS_ADDRESS_LENGTH
        );
        if ($billingAddress->getStreetLine2()) {
            $params['ssl_address2'] = $this->getSubstrMaxValue(
                $billingAddress->getStreetLine2(),
                self::MAX_ADDRESS_LENGTH
            );
        }
        $params['ssl_city'] = $billingAddress->getCity();
        $params['ssl_state'] = !empty($billingAddress->getRegionCode())
            ? (string)$billingAddress->getRegionCode()
            : (string)$billingAddress->getCity();

        $params['ssl_avs_zip'] = $this->getAvsZip($billingAddress->getPostcode());

        if ($billingAddress->getCountryId()) {
            $country = $this->countryInformationAcquirer->getCountryInfo($billingAddress->getCountryId());
            if ($country) {
                $params['ssl_country'] = $country->getThreeLetterAbbreviation();
            }
        }

        $params['ssl_phone'] = $billingAddress->getTelephone();
        $params['ssl_email'] = $billingAddress->getEmail();
    }

    /**
     * @param AddressAdapterInterface $shippingAddress
     * @param array $params
     */
    private function setShippingAddressDetails(AddressAdapterInterface $shippingAddress, array &$params)
    {
        $params['ssl_ship_to_company'] = (string)$shippingAddress->getCompany();
        $params['ssl_ship_to_first_name'] = $this->getSubstrMaxValue(
            $shippingAddress->getFirstName(),
            self::MAX_CUSTOMER_FIRST_NAME_LENGTH
        );
        $params['ssl_ship_to_last_name'] = $this->getSubstrMaxValue(
            $shippingAddress->getLastname(),
            self::MAX_CUSTOMER_LAST_NAME_LENGTH
        );
        $params['ssl_ship_to_address1'] = $this->getSubstrMaxValue(
            $shippingAddress->getStreetLine1(),
            self::MAX_ADDRESS_LENGTH
        );
        if ($shippingAddress->getStreetLine2()) {
            $params['ssl_ship_to_address2'] = $this->getSubstrMaxValue(
                $shippingAddress->getStreetLine2(),
                self::MAX_ADDRESS_LENGTH
            );
        }
        $params['ssl_ship_to_city'] = $shippingAddress->getCity();
        $params['ssl_ship_to_state'] = (string)$shippingAddress->getRegionCode();
        $params['ssl_ship_to_zip'] = $this->getAvsZip($shippingAddress->getPostcode());

        if ($shippingAddress->getCountryId()) {
            $country = $this->countryInformationAcquirer->getCountryInfo($shippingAddress->getCountryId());
            if ($country) {
                $params['ssl_ship_to_country'] = $country->getThreeLetterAbbreviation();
            }
        }

        $params['ssl_ship_to_phone'] = $shippingAddress->getTelephone();
    }

    /**
     * @param string $value
     * @param int $maxLength
     * @return string
     */
    private function getSubstrMaxValue($value, $maxLength)
    {
        return (string)substr($value, 0, $maxLength);
    }

    /**
     * @param string $postcode
     * @return string
     */
    private function getAvsZip($postcode)
    {
        if (false !== strpos($postcode, '-')) {
            return str_replace('-', '', $postcode);
        }
        return $postcode;
    }

    /**
     * @param InfoInterface $payment
     * @return string
     */
    private function getExpDate(InfoInterface $payment)
    {
        $year = $payment->getData(DataAssignObserver::CC_EXP_YEAR);
        $year = strlen($year) === 4 ? substr($year, 2, 2) : $year;

        $month = $payment->getData(DataAssignObserver::CC_EXP_MONTH);
        $month = strlen($month) === 1 ? '0' . $month : $month;

        return $month . $year;
    }
}
