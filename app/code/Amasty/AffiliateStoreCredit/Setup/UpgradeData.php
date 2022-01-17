<?php

namespace Amasty\AffiliateStoreCredit\Setup;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\State;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Amasty\AffiliateStoreCredit\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * InstallData constructor.
     * @param State $appState
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        State $appState,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->appState = $appState;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->appState->emulateAreaCode('adminhtml', [$this, 'createStaticBlock']);
        }

        $this->setup->endSetup();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createStaticBlock()
    {
        /** @var \Magento\Cms\Model\Block $affiliateStaticBlock */
        $affiliateBalanceStaticBlock = $this->blockFactory->create()->load('amasty-affiliate-balance-static-block', 'identifier');

        if (!$affiliateBalanceStaticBlock->getData()) {
            $affilateBalanceStaticContent =
                <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOD;
            $affiliateBalanceStaticBlock = [
                'title' => 'Amasty Affiliate Balance Static Block',
                'identifier' => 'amasty-affiliate-balance-static-block',
                'content' => $affilateBalanceStaticContent,
                'stores' => [0],
                'is_active' => 1,
            ];

            $affiliateBalanceStaticBlock = $this->blockFactory->create()->setData($affiliateBalanceStaticBlock);
            $this->blockRepository->save($affiliateBalanceStaticBlock);
        }
    }
}
