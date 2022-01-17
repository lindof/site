<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Affiliate
 */

namespace Amasty\AffiliateStoreCredit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Amasty\AffiliateStoreCredit\Model
 */
class Config
{
    const AMASTY_AFFILIATE_CRON_ENABLE = 'amasty_affiliate/cron/enable';

    const AMASTY_AFFILIATE_MINIMUM_AFFILATE_BALANCE = 'amasty_affiliate/cron/minimum_affiliate_balance';

    /**
     * @return mixed
     */

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return (bool)$this->scopeConfig->getValue(
            self::AMASTY_AFFILIATE_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMinimumAffiliateBalance()
    {
        return $this->scopeConfig->getValue(
            self::AMASTY_AFFILIATE_MINIMUM_AFFILATE_BALANCE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
