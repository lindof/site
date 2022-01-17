<?php
namespace Mexbs\ApBase\Model\Plugin;

class Rule
{
    private $extensionFactory;
    public function __construct(
        \Magento\SalesRule\Api\Data\RuleExtensionFactory $extensionFactory
    ){
        $this->extensionFactory = $extensionFactory;
    }

    public function afterGetExtensionAttributes(
        \Magento\SalesRule\Api\Data\RuleInterface $entity,
        \Magento\SalesRule\Api\Data\RuleExtensionInterface $extension = null
    )
    {
        if ($extension === null) {
            $extension = $this->extensionFactory->create();
        }

        return $extension;
    }
}
