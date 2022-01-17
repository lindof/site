<?php

namespace IWD\StoreLocator\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Directory\Model\ResourceModel\Country\Collection;

/**
 * Class Status
 */
class Country extends Column
{
    /**
     * @var string[]
     */
    private $countries;

    /**
     * Country constructor.
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

        $this->countries = $res;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')]) && isset($this->countries[$item[$this->getData('name')]])) {
                    $item[$this->getData('name')] = $this->countries[$item[$this->getData('name')]];
                }
            }
        }

        return $dataSource;
    }
}
