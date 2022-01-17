<?php

namespace Mexbs\ApBase\Model\Import;

use Magento\ImportExport\Helper\Data as DataHelper;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;

class Sources extends AbstractEntity
{
    private $missingHeaders;
    private $redundantHeaders;
    private $connection;
    private $indexerFactory;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceHelper $resourceHelper,
        DataHelper $dataHelper,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->errorAggregator = $errorAggregator;
        $this->_resourceHelper = $resourceHelper;
        $this->_importExportData = $dataHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->indexerFactory = $indexerFactory;
        $this->connection = $resource->getConnection();
    }

    protected function changeNullsToZeroesInNumericFields($salesRuleDataItem){
        $numericFields = [
            'max_groups_number',
            'max_sets_number',
            'max_discount_amount',
            'skip_special_price',
            'skip_tier_price',
            'display_popup_on_first_visit',
            'display_cart_hints',
            'hide_hints_after_discount_number',
            'display_cart_hints_if_coupon_invalid',
            'hide_promo_block_if_rule_applied',
            'display_product_hints',
            'enable_auto_add',
            'display_banner_in_promo_trigger_products',
            'display_banner_in_get_products',
            'display_badge_in_get_products',
            'display_badge_in_promo_trigger_products_category',
            'display_badge_in_get_products_category',
            'display_promo_block_in_cart',
            'display_promo_block_in_product',
        ];
        $salesRuleDataItemFiltered = $salesRuleDataItem;
        foreach($salesRuleDataItemFiltered as $fieldKey => $fieldValue){
            if(in_array($fieldKey, $numericFields) && ($fieldValue == null)){
                $salesRuleDataItemFiltered[$fieldKey] = 0;
            }
        }

        return $salesRuleDataItemFiltered;
    }

    protected function changeNullsToEmptyStringInNonRequiredVarcharFields($salesRuleDataItem){
        $numericFields = [
            'popup_on_first_visit_image',
            'actions_hint_label',
            'product_hints_location',
            'banner_in_promo_trigger_products_image',
            'badge_in_promo_trigger_products_image',
            'banner_in_get_products_image',
            'badge_in_get_products_image',
            'badge_in_promo_trigger_products_category_image',
            'badge_in_get_products_category_image',
            'promo_block_title_in_product',
            'promo_block_set_name'
        ];
        $salesRuleDataItemFiltered = $salesRuleDataItem;
        foreach($salesRuleDataItemFiltered as $fieldKey => $fieldValue){
            if(in_array($fieldKey, $numericFields) && ($fieldValue == null)){
                $salesRuleDataItemFiltered[$fieldKey] = "";
            }
        }

        return $salesRuleDataItemFiltered;
    }

    protected function _importData()
    {
        $behavior = $this->getBehavior();

        $affectedRuleIds = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {

            if($behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE
                || $behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND
                || $behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE){

                foreach($bunch as $salesRuleDataItem){
                    $salesRuleDataItemClean = $salesRuleDataItem;

                    $customerGroupIds = null;
                    if(isset($salesRuleDataItem[\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS])){
                        $customerGroupIds = $salesRuleDataItem[\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS];
                        unset($salesRuleDataItemClean[\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS]);
                    }

                    $websiteIds = null;
                    if(isset($salesRuleDataItem[\Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS])){
                        $websiteIds = $salesRuleDataItem[\Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS];
                        unset($salesRuleDataItemClean[\Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS]);
                    }


                    $salesRuleDataItemClean = $this->changeNullsToZeroesInNumericFields($salesRuleDataItemClean);
                    $salesRuleDataItemClean = $this->changeNullsToEmptyStringInNonRequiredVarcharFields($salesRuleDataItemClean);


                    if(!isset($salesRuleDataItemClean['rule_id']) || !is_numeric($salesRuleDataItemClean['rule_id']) || !$salesRuleDataItemClean['rule_id']){
                        try{
                            $this->connection->beginTransaction();

                            $this->connection->insert($this->resource->getTableName('salesrule'), $salesRuleDataItemClean);

                            $insertedRuleId = $this->connection->lastInsertId($this->resource->getTableName('salesrule'));

                            foreach(explode(",", $customerGroupIds) as $customerGroupId){
                                $this->connection->insert($this->resource->getTableName('salesrule_customer_group'), [
                                    'rule_id' => $insertedRuleId,
                                    'customer_group_id' => $customerGroupId,
                                ]);
                            }

                            foreach(explode(",", $websiteIds) as $websiteId){
                                $this->connection->insert($this->resource->getTableName('salesrule_website'), [
                                    'rule_id' => $insertedRuleId,
                                    'website_id' => $websiteId,
                                ]);
                            }

                            $affectedRuleIds[] = $insertedRuleId;

                            $this->connection->commit();
                        }catch(\Exception $e){
                            $this->connection->rollBack();
                            throw $e;
                        }
                    }else{
                        try{
                            $this->connection->update($this->resource->getTableName('salesrule'), $salesRuleDataItemClean, sprintf("rule_id=%s", $salesRuleDataItemClean['rule_id']));

                            if($customerGroupIds){
                                $this->connection->delete(
                                    $this->resource->getTableName('salesrule_customer_group'),
                                    sprintf("rule_id=%s", $salesRuleDataItemClean['rule_id'])
                                );

                                foreach(explode(",", $customerGroupIds) as $customerGroupId){
                                    $this->connection->insert($this->resource->getTableName('salesrule_customer_group'),
                                        [
                                            'rule_id' => $salesRuleDataItemClean['rule_id'],
                                            'customer_group_id' => $customerGroupId,
                                        ]
                                    );
                                }
                            }

                            if($websiteIds){
                                $this->connection->delete(
                                    $this->resource->getTableName('salesrule_website'),
                                    sprintf("rule_id=%s", $salesRuleDataItemClean['rule_id'])
                                );

                                foreach(explode(",", $websiteIds) as $websiteId){
                                    $this->connection->insert($this->resource->getTableName('salesrule_website'),
                                        [
                                            'rule_id' => $salesRuleDataItemClean['rule_id'],
                                            'website_id' => $websiteId,
                                        ]
                                    );
                                }
                            }

                            $affectedRuleIds[] = $salesRuleDataItemClean['rule_id'];

                        }catch(\Exception $e){
                            $this->connection->rollBack();
                            throw $e;
                        }
                    }
                }
            }elseif($behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE){
                $salesRulesIds = [];
                foreach($bunch as $salesRuleDataItem){
                    if(isset($salesRuleDataItem['rule_id'])){
                        $salesRulesIds[] = $salesRuleDataItem['rule_id'];
                    }
                }

                if(count($salesRulesIds)){
                    $this->connection->delete(
                        $this->resource->getTableName('apactionrule_product'),
                        $this->connection->quoteInto('rule_id IN (?)', $salesRulesIds)
                    );
                }
            }
        }

        if(count($affectedRuleIds)){
            $this->indexerFactory->create()->load(\Mexbs\ApBase\Model\Indexer\Rule\RuleProductProcessor::INDEXER_ID)->reindexList($affectedRuleIds);
        }

        return true;
    }

    public function getEntityTypeCode()
    {
        return 'mexbs_cart_rules';
    }

    protected function _getHeaderColumns()
    {
        return array_merge($this->_getSalesRuleColumns(), [\Mexbs\ApBase\Helper\ImportExport::COLUMN_CUSTOMER_GROUP_IDS, \Mexbs\ApBase\Helper\ImportExport::COLUMN_WEBSITE_IDS]);
    }

    protected function _getSalesRuleColumns()
    {
        return array_keys($this->connection->describeTable($this->resource->getTableName("salesrule")));
    }

    protected function getMissingHeaders($columnNames){
        if(!$this->missingHeaders){
            $this->missingHeaders = array_diff($this->_getHeaderColumns(), $columnNames);
        }
        return $this->missingHeaders;
    }

    protected function getRedundantHeaders($columnNames){
        if(!$this->redundantHeaders){
            $this->redundantHeaders = array_diff($columnNames, $this->_getHeaderColumns());
        }
        return $this->redundantHeaders;
    }


    public function validateRow(array $rowData, $rowNum)
    {
        $behavior = $this->getBehavior();

        $isValid = true;

        if($behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE){
            $missingHeaders = $this->getMissingHeaders($this->getSource()->getColNames());
            if(count($missingHeaders)){
                foreach($missingHeaders as $missingHeader){
                    $this->addRowError(self::ERROR_CODE_COLUMN_NOT_FOUND, $rowNum, $missingHeader);
                    $isValid = false;
                }
            }
            $redundantHeaders = $this->getRedundantHeaders($this->getSource()->getColNames());
            if(count($redundantHeaders)){
                foreach($redundantHeaders as $redundantHeader){
                    $this->addRowError(self::ERROR_CODE_COLUMN_NAME_INVALID, $rowNum, $redundantHeader);
                    $isValid = false;
                }
            }
        }elseif($behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE
            || $behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND){

            $redundantHeaders = $this->getRedundantHeaders($this->getSource()->getColNames());
            if(count($redundantHeaders)){
                foreach($redundantHeaders as $redundantHeader){
                    $this->addRowError(self::ERROR_CODE_COLUMN_NAME_INVALID, $rowNum, $redundantHeader);
                    $isValid = false;
                }
            }
        }elseif($behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE){
            if(!isset($rowData['rule_id']) || !$rowData['rule_id']){
                $this->addRowError(self::ERROR_CODE_COLUMN_NOT_FOUND, $rowNum, "rule_id");
                $isValid = false;
            }
        }

        return $isValid;
    }
}
