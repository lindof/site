<?php

namespace Orange35\ImageConstructor\Model;

use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;
use Zend_Db;
use Zend_Db_Select;

class Layer
{
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Return a list of layers like ['id' => 1, 'image' => 'a/b/abc.png']
     * @param int|array $id - single or multiple custom option value id
     * @return array|array[] - returns a single row e.g. ['id' => 1, 'image' => 'a/b/abc.png'] if $id is numeric
     * or a list of rows e.g. [row1, row2, row2] if $id is an array
     */
    public function getById($id)
    {
        $select = clone $this->collection->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(['id' => 'option_type_id', 'image' => 'layer']);
        $select->join(['o' => 'catalog_product_option'], '`o`.`option_id` = `main_table`.`option_id`', []);
        $select->where('option_type_id IN (?)', (array) $id, Zend_Db::INT_TYPE);
        $select->order('o.sort_order');
        return is_array($id) ? $select->getAdapter()->fetchAll($select) : $select->getAdapter()->fetchRow($select);
    }
}
