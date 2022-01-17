<?php
namespace IWD\StoreLocator\Api\Data;

/**
 * Factory class for @see \IWD\StoreLocator\Api\Data\ItemSearchResultsInterface
 */
class ItemSearchResultsInterfaceFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\IWD\\StoreLocator\\Api\\Data\\ItemSearchResultsInterface')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \IWD\StoreLocator\Api\Data\ItemSearchResultsInterface
     */
    public function create(array $data = [])
    {        
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
