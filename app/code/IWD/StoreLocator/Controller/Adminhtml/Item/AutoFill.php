<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use IWD\StoreLocator\Model\GeoLocation;
use IWD\StoreLocator\Helper\Config;

/**
 * Class AutoFill
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class AutoFill extends Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var GeoLocation
     */
    private $geoLocation;
    private $config;

    /**
     * AutoFill constructor.
     * @param Action\Context $context
     * @param GeoLocation $geoLocation
     * @param Config $config
     */
    public function __construct(
        Action\Context $context,
        GeoLocation $geoLocation,
        Config $config
    )
    {
        $this->geoLocation = $geoLocation;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->geoLocation->fillGeoData();
            $this->messageManager->addSuccessMessage(__('Geo data have been updated successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
