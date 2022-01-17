<?php

namespace IWD\StoreLocator\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Documentation
 * @package IWD\StoreLocator\Block\Adminhtml\System\Config
 */
class Documentation extends Field
{
    private $userGuideUrl = "https://iwdagency.com/help/m2-store-locator/store-2-settings";

    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            "<span style='margin-bottom:-8px; display:block;'><a href='%s'>%s</a></span>",
            $this->userGuideUrl,
            __("User Guide")
        );
    }
}
