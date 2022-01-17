<?php
// @codingStandardsIgnoreFile

namespace Mexbs\ApBase;

class Serialize
{
    protected $productMetaData;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData
    )
    {
        $this->productMetaData = $productMetaData;
    }

    public function serialize($value){
        if(version_compare($this->productMetaData->getVersion(), "2.2.0", "<")){
            return serialize($value);
        }else{
            return \Magento\Framework\App\ObjectManager::getInstance()->get("Magento\Framework\Serialize\Serializer\Json")->serialize($value);
        }
    }

    public function unserialize($value){
        if(version_compare($this->productMetaData->getVersion(), "2.2.0", "<")){
            return unserialize($value);
        }else{
            try{
                return \Magento\Framework\App\ObjectManager::getInstance()->get("Magento\Framework\Serialize\Serializer\Json")->unserialize($value);
            }catch(\Exception $e){
                return \Magento\Framework\App\ObjectManager::getInstance()->get("Magento\\Framework\\Serialize\\Serializer\\Serialize")->unserialize($value);
            }
        }
    }
}
