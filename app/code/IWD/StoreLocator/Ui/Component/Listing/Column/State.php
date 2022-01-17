<?php

namespace IWD\StoreLocator\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Directory\Model\ResourceModel\Region\Collection;

/**
 * Class Status
 */
class State extends Column
{
    /**
     * @var string[]
     */
    private $regions;

    /**
     * State constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Collection $collection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Collection $collection,
        array $components = [],
        array $data = []
    ) {
        $options = $collection->load()->toOptionArray();

        $res = [];
        foreach ($options as $item) {
            $res[$item['value']] = $item['label'];
        }

        $this->regions = $res;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (empty($item['region_id']) && !empty($item['region'])) {
                    $item['region_id'] = $item['region'];
                } else {
                    if (isset($this->regions[$item[$this->getData('name')]])) {
                        $item[$this->getData('name')] = $this->regions[$item[$this->getData('name')]];
                    }
                }
            }
        }

        return $dataSource;
    }
}
