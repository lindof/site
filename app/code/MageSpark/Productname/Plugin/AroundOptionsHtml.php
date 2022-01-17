<?php 

namespace MageSpark\Productname\Plugin;

class AroundOptionsHtml extends \MageWorx\OptionBase\Plugin\AroundOptionsHtml
{
	protected function getInnerHtml(\DOMElement $node, \Magento\Catalog\Model\Product\Option $option)
    {
    	return str_replace('{label}', strtolower($option->getTitle()), parent::getInnerHtml($node,$option));
    }
}