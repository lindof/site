<?php
namespace Mexbs\ApBase\Block\Widget;

use \Magento\Framework\View\Element\Template;

class PromosList extends \Mexbs\ApBase\Block\PromoProducts implements \Magento\Widget\Block\BlockInterface
{
    public function __construct(
        \Mexbs\ApBase\Helper\Data $helper,
        Template\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    )
    {
        parent::__construct(
            $helper,
            $context,
            $cart,
            $data
        );
        $this->setTemplate('Mexbs_ApBase::widget/inject-promo-products.phtml');
    }
}