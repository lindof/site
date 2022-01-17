<?php
namespace Mexbs\ApBase\Model\Plugin\Quote;

class Address
{
    protected $serializer;

    public function __construct(
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function beforeSave(
        \Magento\Quote\Model\Quote\Address $address
    ){
        $discountDetails = $address->getDiscountDetails();
        if(is_array($discountDetails)){
            $address->setDiscountDetails($this->serializer->serialize($discountDetails));
        }
    }
}