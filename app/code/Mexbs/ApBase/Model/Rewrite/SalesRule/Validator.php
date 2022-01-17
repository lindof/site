<?php
namespace Mexbs\ApBase\Model\Rewrite\SalesRule;

class Validator extends \Magento\SalesRule\Model\Validator
{
    public function getRulesItemTotalsInfo()
    {
        return $this->_rulesItemTotals;
    }
    public function setRulesItemTotalsInfo($totalsInfo)
    {
        $this->_rulesItemTotals = $totalsInfo;
    }
}
