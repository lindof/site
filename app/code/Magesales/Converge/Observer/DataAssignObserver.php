<?php


namespace Magesales\Converge\Observer;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**#@+
     * Payment fields constants
     */
    const CC_NUMBER = 'cc_number';
    const CC_LAST_4 = 'cc_last_4';
    const CC_TYPE = 'cc_type';
    const CC_EXP_DATE = 'cc_exp_date';
    const CC_EXP_MONTH = 'cc_exp_month';
    const CC_EXP_YEAR = 'cc_exp_year';
    const CC_CID = 'cc_cid';
    const CC_CVV_INDICATOR = 'cvv_indicator';
    /**#@-*/

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * DataAssignObserver constructor.
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $additionalData = $this->readDataArgument($observer)->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readMethodArgument($observer)->getInfoInstance();

        if (!$paymentInfo instanceof InfoInterface) {
            throw new LocalizedException(__('Payment Info model does not provided.'));
        }

        $additionalData = $this->dataObjectFactory->create(['data' => $additionalData]);
        $paymentInfo->setData(self::CC_NUMBER, $additionalData->getData('cc_number'));
        $paymentInfo->setData(self::CC_LAST_4, substr($additionalData->getData('cc_number'), -4));
        $paymentInfo->setData(self::CC_TYPE, $additionalData->getData('cc_type'));
        $paymentInfo->setData(self::CC_EXP_MONTH, $additionalData->getData('cc_exp_month'));
        $paymentInfo->setData(self::CC_EXP_YEAR, $additionalData->getData('cc_exp_year'));
        $paymentInfo->setData(self::CC_CVV_INDICATOR, $additionalData->getData('cc_cid') ? 1 : 0);
        $paymentInfo->setData(self::CC_CID, $additionalData->getData('cc_cid'));
    }
}
