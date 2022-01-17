<?php

namespace Orange35\ImageConstructor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Orange35\ImageConstructor\Helper\OrderImage as ImageHelper;

class OrderItemSave implements ObserverInterface
{
    protected $imageHelper;

    public function __construct(ImageHelper $imageHelper)
    {
        $this->imageHelper = $imageHelper;
    }

    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var \Magento\Sales\Model\Order\Item $item */
        $item = $event->getItem();
        if (!$item->getId() && !$item->getData('image')) {
            $image = $this->imageHelper->setItem($item)->create();
            $item->setData('image', $image);
        }
        return $this;
    }
}
