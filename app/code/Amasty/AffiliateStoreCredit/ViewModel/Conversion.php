<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Affiliate
 */


namespace Amasty\AffiliateStoreCredit\ViewModel;


use Amasty\Affiliate\Api\AccountRepositoryInterface;
use Magento\Customer\Model\Session;
use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Conversion
 * @package Amasty\AffiliateStoreCredit\ViewModel
 */
class Conversion implements ArgumentInterface
{
    /**
     * @var AccountRepositoryInterface
     */
    private $affiliateAccountRepository;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Conversion constructor.
     * @param AccountRepositoryInterface $affiliateAccountRepository
     * @param Session $customerSession
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        AccountRepositoryInterface $affiliateAccountRepository,
        Session $customerSession,
        StoreCreditRepositoryInterface $storeCreditRepository,
        PriceCurrencyInterface $priceCurrency,
        UrlInterface $urlBuilder
    ) {
        $this->affiliateAccountRepository = $affiliateAccountRepository;
        $this->customerSession = $customerSession;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceCurrency = $priceCurrency;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return \Amasty\Affiliate\Model\Account
     */
    public function getAffiliateAccount()
    {
        /** @var \Amasty\Affiliate\Model\Account $account */
        $account = $this->affiliateAccountRepository->getCurrentAccount();
        $account->preparePrices();

        return $account;
    }

    /**
     * @return mixed
     */
    public function getAffiliateBalanceWithCurrency()
    {
        return $this->getAffiliateAccount()->getData('balance_with_currency');
    }

    /**
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function getStoreCreditAccount()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $this->storeCreditRepository->getByCustomerId($customerId);
    }

    /**
     * @return string
     */
    public function getStoreCreditBalanceWithCurrency()
    {
        return $this->priceCurrency->convertAndFormat(
            $this->getStoreCreditAccount()->getStoreCredit(),
            false,
            2
        );
    }

    /**
     * Return the Url for conversion.
     *
     * @return string
     */
    public function getConversionUrl()
    {
        return $this->urlBuilder->getUrl(
            'amasty_affiliate/account/converter',
            ['_secure' => true, 'customer_id' => $this->customerSession->getCustomerId()]
        );
    }
}
