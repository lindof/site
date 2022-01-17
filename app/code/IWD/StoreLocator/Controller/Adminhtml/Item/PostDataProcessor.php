<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Model\Layout\Update\ValidatorFactory;

/**
 * Class PostDataProcessor
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class PostDataProcessor
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ValidatorFactory
     */
    private $validatorFactory;

    /**
     * @var Date
     */
    private $dateFilter;

    /**
     * @param Date $dateFilter
     * @param ManagerInterface $messageManager
     * @param ValidatorFactory $validatorFactory
     */
    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager,
        ValidatorFactory $validatorFactory
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        $errorNo = true;
        if (!isset($data['custom_layout_update_xml']) || !isset($data['layout_update_xml'])) {
            return $errorNo;
        }

        $customLayoutUpdateXml = $data['custom_layout_update_xml'];
        $layoutUpdateXml = $data['layout_update_xml'];
        if (!empty($customLayoutUpdateXml) || !empty($layoutUpdateXml)) {
            $validatorCustomLayout = $this->validatorFactory->create();

            if (!empty($layoutUpdateXml) && !$validatorCustomLayout->isValid($layoutUpdateXml)) {
                $errorNo = false;
            }

            if (!empty($customLayoutUpdateXml) && !$validatorCustomLayout->isValid($customLayoutUpdateXml)) {
                $errorNo = false;
            }

            $messages = $validatorCustomLayout->getMessages();
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }
        return $errorNo;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'title' => __('Page Title'),
            'is_active' => __('Status'),
            'stores' => __('Store View'),
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            $isRequired = in_array($field, array_keys($requiredFields));
            if ($isRequired && $value == '') {
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in hidden required "%1" field', $requiredFields[$field])
                );
                $errorNo = false;
            }
        }
        return $errorNo;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function filter($data)
    {
        $filter = [
            'custom_theme_from' => $this->dateFilter,
            'custom_theme_to' => $this->dateFilter
        ];

        $inputFilter = new \Zend_Filter_Input($filter, [], $data);
        $data = $inputFilter->getUnescaped();

        return $data;
    }
}
