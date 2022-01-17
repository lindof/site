<?php
namespace Mexbs\ApBase\Model\SalesRule\Rule\Condition\Customer;

class Birthday extends \Magento\Rule\Model\Condition\AbstractCondition
{
    protected $apHelper;
    protected $orderStatusesOptionArray;
    protected $dateTime;
    protected $customerFactory;
    protected $customerSession;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Mexbs\ApBase\Helper\Data $apHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->apHelper = $apHelper;
        $this->dateTime = $dateTime;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
    }

    public function loadAttributeOptions()
    {
        $attributes = [
            'birthday' => __('Customer Birthday')
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getValueSelectOptions()
    {
        return [
            [
                'value' => 'today',
                'label' => 'today'
            ],
            [
                'value' => 'week_range',
                'label' => 'in a week range'
            ],
            [
                'value' => 'two_weeks_range',
                'label' => 'in two weeks range'
            ],
            [
                'value' => 'this_month',
                'label' => 'this month'
            ],
        ];
    }

    public function getOperatorSelectOptions()
    {
        $opt = [];
        $operators = ["==" => "is","!=" => "is not"];
        foreach ($operators as $key => $value) {
            $opt[] = ['value' => $key, 'label' => $value];
        }
        return $opt;
    }

    public function getValueElement(){
        $element = parent::getValueElement();
        $element->setRenderer($this->_layout->getBlockSingleton('Magento\Rule\Block\Editable'));
        return $element;
    }

    public function getOperatorElement()
    {
        $element = parent::getOperatorElement();
        $element->setRenderer($this->_layout->getBlockSingleton('Magento\Rule\Block\Editable'));
        return $element;
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $addressCustomerId = $model->getCustomerId();
        if(!$addressCustomerId){
            return false;
        }

        $loggedInCustomerId = $this->customerSession->getCustomerId();
        if(!$loggedInCustomerId
            || ($addressCustomerId != $loggedInCustomerId)){
            return false;
        }

        $customer = $this->customerFactory->create()->load($addressCustomerId);

        $dob = $customer->getDob();
        $dobTimestamp = strtotime($dob);
        if(!$dobTimestamp){
            return false;
        }

        $dobDay = date('d', $dobTimestamp);
        $dobMonth = date('m', $dobTimestamp);

        $currentDateTimestamp = $this->dateTime->gmtTimestamp();
        $currentYear = date('Y', $currentDateTimestamp);

        $dobTimestampThisYear = strtotime($dobDay.".".$dobMonth.".".$currentYear);

        $differenceInDays = abs(floor(($currentDateTimestamp-$dobTimestampThisYear)/60/60/24));

        $desiredDifferenceValue = $this->getValueParsed();

        if($desiredDifferenceValue == "today"){
            if($differenceInDays == 0){
                return true;
            }else{
                return false;
            }
        }

        if($desiredDifferenceValue == "week_range"){
            if($differenceInDays <= 3){
                return true;
            }else{
                return false;
            }
        }

        if($desiredDifferenceValue == "two_weeks_range"){
            if($differenceInDays <= 7){
                return true;
            }else{
                return false;
            }
        }

        $currentMonth = date("m", $currentDateTimestamp);

        if($desiredDifferenceValue == "this_month"){
            if($currentMonth == $dobMonth){
                return true;
            }else{
                return false;
            }
        }

        return $this->validateAttribute($differenceInDays);
    }
}
