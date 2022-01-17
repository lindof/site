<?php

namespace IWD\StoreLocator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Item
 * @package IWD\StoreLocator\Model\ResourceModel
 */
class Item extends AbstractDb
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);

        $this->storeManager = $storeManager;
        $this->date = $date;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('storelocator_item', 'item_id');
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $condition = ['item_id = ?' => (int)$object->getId()];
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('storelocator_item_store'),
            $condition
        );

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setCreationTime($this->date->gmtDate());
        }
        $object->setUpdateTime($this->date->gmtDate());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (is_string($object->getStores()))
            ? explode(',', $object->getStores())
            : (array)$object->getStores();

        $table = $this->getTable('storelocator_item_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        $itemId = (int)$object->getId();

        if ($delete) {
            $where = [
                'item_id = ?' => $itemId,
                'store_id IN (?)' => $delete
            ];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = [
                    'item_id' => $itemId,
                    'store_id' => (int)$storeId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * {@inheritdoc}
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];
            $name = ['cbs' => $this->getTable('storelocator_item_store')];
            $cond = $this->getMainTable() . '.item_id = cbs.item_id';

            $select->join($name, $cond, ['store_id'])
                ->where('is_active = ?', 1)
                ->where('cbs.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable('storelocator_item_store'), 'store_id')
            ->where('item_id = :item_id');

        return $connection->fetchCol($select, [':item_id' => (int)$id]);
    }
}
