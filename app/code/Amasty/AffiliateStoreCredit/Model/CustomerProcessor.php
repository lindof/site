<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Affiliate
 */

namespace Amasty\AffiliateStoreCredit\Model;

use Amasty\Affiliate\Api\AccountRepositoryInterface;
use Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface;
use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;

class CustomerProcessor
{
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        StoreCreditRepositoryInterface $storeCreditRepository,
        ManageCustomerStoreCreditInterface $manageCustomerStoreCredit,
        ManagerInterface $messageManager
    ) {
        $this->accountRepository = $accountRepository;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->messageManager = $messageManager;
    }

    public function convertByCustomerId($customerId)
    {
        /** @param \Amasty\Affiliate\Api\Data\AccountInterface $affilateAccount */
        $affilateAccount = $this->accountRepository->getByCustomerId($customerId);

        if ($affilateAccount->getBalance() > 0) {
            $storeCreditAccount = $this->storeCreditRepository->getByCustomerId($customerId);
            $currentStoreCreditBalance = $storeCreditAccount->getStoreCredit();
            $addOrSubtract = $affilateAccount->getBalance();

            if (($currentStoreCreditBalance + $addOrSubtract) < 0) {
                $addOrSubtract = -$currentStoreCreditBalance;
            }

            try {
                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $customerId,
                    $addOrSubtract,
                    0,
                    [],
                    null,
                    ''
                );
                $affilateAccount->setBalance(0);
                $this->accountRepository->save($affilateAccount);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Something wrong during conversion.'));
            }
        }
    }
}
