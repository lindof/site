<?php
namespace Mexbs\ApBase\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;


class Sources extends AbstractEntity
{
    private $salesRuleCollectionFactory;
    private $resource;
    private $connection;
    private $collectionFactory;
    private $attributeFactory;
    private $helper;
    private $productMetaData;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $salesRuleCollectionFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mexbs\ApBase\Helper\Data $helper,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        array $data = []
    ) {
        $this->salesRuleCollectionFactory = $salesRuleCollectionFactory;
        $this->resource = $resource;
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->attributeFactory = $attributeFactory;
        $this->productMetaData = $productMetaData;
        $this->connection = $resource->getConnection();
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }

    public function getMagentoVersion(){
        return $this->productMetaData->getVersion();
    }

    public function getAttributeCollection()
    {
        $collection = $this->collectionFactory->create(\Magento\Framework\Data\Collection::class);

        $sourceCodeAttribute = $this->attributeFactory->create();
        $sourceCodeAttribute->setId("name");
        $sourceCodeAttribute->setDefaultFrontendLabel("name");
        $sourceCodeAttribute->setAttributeCode("name");
        $sourceCodeAttribute->setBackendType('varchar');
        $collection->addItem($sourceCodeAttribute);

        return $collection;
    }

    public function export()
    {
        $writer = $this->getWriter();

        $headerColumns = $this->_getHeaderColumns();
        $writer->setHeaderCols($headerColumns);

        /**
         * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $salesRuleCollection
         */
        $salesRuleCollection = $this->salesRuleCollectionFactory->create();

        if(isset($this->_parameters['export_filter']['name'])){
            $ruleNameFilter = $this->_parameters['export_filter']['name'];
            if(trim($ruleNameFilter) != ""){
                $salesRuleCollection->addFieldToFilter("name", ["like" => "%".$ruleNameFilter."%"]);
            }
        }

        /**
         * @var \Magento\SalesRule\Model\Rule $salesRule
         */
        foreach ($salesRuleCollection as $salesRule) {
            $salesRuleFields = $salesRule->toArray();

            $salesRuleFields[\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS] = implode(",",$salesRule->getCustomerGroupIds());
            $salesRuleFields[\Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS] = implode(",",$salesRule->getWebsiteIds());

            $writer->writeRow($salesRuleFields);
        }

        return $writer->getContents();
    }

    protected function _getSalesRuleColumns()
    {
        return array_keys($this->connection->describeTable($this->resource->getTableName("salesrule")));
    }

    protected function _getHeaderColumns()
    {
        return array_merge($this->_getSalesRuleColumns(), [\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS, \Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function exportItem($item)
    {
        // will not implement this method as it is legacy interface
    }

    public function getEntityTypeCode()
    {
        return 'mexbs_cart_rules';
    }


    protected function _getEntityCollection()
    {
        // will not implement this method as it is legacy interface
    }
}
