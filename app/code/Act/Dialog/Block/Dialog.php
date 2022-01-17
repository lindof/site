<?php

namespace Act\Dialog\Block;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Dialog extends Template
{
    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var BlockInterface
     */
    protected $cmsBlock;

    /**
     * @param Context $context
     * @param BlockRepositoryInterface $blockRepository
     * @throws LocalizedException
     */
    public function __construct(
        Context               $context,
        BlockRepositoryInterface       $blockRepository
    ) {
        $this->blockRepository       = $blockRepository;

        /** @var Block $block */
        $this->cmsBlock = $this->blockRepository->getById('dialog_box');

        parent::__construct($context);
    }

    /**
     * The Block CMS Content
     */
    public function getCmsBlockContent()
    {
        /** @var Block $block */
        $block = $this->cmsBlock;
        return $block->getData('content');
    }

    /**
     * Is the CMS Block active
     */
    public function isActive()
    {
        /** @var Block $block */
        $block = $this->cmsBlock;
        return $block->getData('is_active');
    }

    /**
     * Is the CMS Block active
     */
    public function getTitle()
    {
        /** @var Block $block */
        $block = $this->cmsBlock;
        return $block->getData('title');
    }

}
