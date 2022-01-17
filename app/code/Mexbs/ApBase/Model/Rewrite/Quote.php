<?php
namespace Mexbs\ApBase\Model\Rewrite;

class Quote extends \Magento\Quote\Model\Quote
{
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $quoteItem) {
            if ($quoteItem->getId() == $itemId) {
                return $quoteItem;
            }
        }

        return false;
    }
}