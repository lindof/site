<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Model\ResourceModel\Paymentrestrictions;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'pr_id';
   
    protected function _construct()
    {
        $this->_init(
            'Cynoinfotech\PaymentRestrictions\Model\Paymentrestrictions',
            'Cynoinfotech\PaymentRestrictions\Model\ResourceModel\Paymentrestrictions'
        );
    }
}
