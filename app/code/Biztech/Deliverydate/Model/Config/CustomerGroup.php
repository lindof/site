<?php
namespace Biztech\Deliverydate\Model\Config;

class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroupColl;
        
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupColl
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupColl
    ) {
        $this->_customerGroupColl = $customerGroupColl;
    }

    /**
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        $customerGroups = $this->_customerGroupColl->toOptionArray();
        return $customerGroups;
    }

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->getCustomerGroups();
        }
        return $this->_options;
    }
}
