<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;

class Paymentrestrictions extends \Magento\SalesRule\Model\Rule
{
     /**
     * Initialize resource model
     *
     * @return void
     */
    const STATUS_ENABLED = 1;
	
    const STATUS_DISABLED = 0;
    
    protected function _construct()
    {
        $this->_init('Cynoinfotech\PaymentRestrictions\Model\ResourceModel\Paymentrestrictions');
    }
    
}