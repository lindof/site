<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Affiliate
 */


namespace Amasty\AffiliateStoreCredit\Cron;


use Amasty\Affiliate\Model\ResourceModel\Account\CollectionFactory;
use Amasty\Affiliate\Api\Data\AccountInterface;
use Amasty\AffiliateStoreCredit\Model\Config;
use Amasty\AffiliateStoreCredit\Model\CustomerProcessor;
use Psr\Log\LoggerInterface as PsrLogger;

class ConversionProcess
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CustomerProcessor
     */
    private $customerProcessor;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PsrLogger
     */
    private $logger;

    public function __construct(
        CollectionFactory $collectionFactory,
        CustomerProcessor $customerProcessor,
        Config $config,
        PsrLogger $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerProcessor = $customerProcessor;
        $this->config = $config;
        $this->logger = $logger;
    }
    public function execute()
    {
        if (!$this->config->isEnable()) {
            return;
        }

        try {
            $affilateAccountCollection = $this->collectionFactory->create();
            $affilateAccountCollection->addFieldToFilter(AccountInterface::IS_AFFILIATE_ACTIVE, 1);
            $affilateAccountCollection->addFieldToFilter(AccountInterface::BALANCE, array('gteq' => $this->config->getMinimumAffiliateBalance()));

            foreach ($affilateAccountCollection->getItems() as $affilateAccount) {
                /** @param \Amasty\Affiliate\Api\Data\AccountInterface $affilateAccount */
                $this->customerProcessor->convertByCustomerId($affilateAccount->getCustomerId());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
