<?php
namespace Mexbs\ApBase\Model\Rule;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    private $apHelper;
    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->apHelper = $apHelper;
        $this->name = $name;
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->meta = $meta;
        $this->data = $data;
    }

    public function getData()
    {
        return array_keys($this->apHelper->getApSimpleActionsToTypes());
    }
}