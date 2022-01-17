<?php

namespace IWD\StoreLocator\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Class Cron
 * @package IWD\StoreLocator\Model\Config\Backend
 */
class Cron extends Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/iwd_storelocator_fill/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/default/jobs/iwd_storelocator_fill/run/model';

    const XML_PATH_BACKUP_ENABLED = 'groups/auto_fill/fields/enabled/value';
    const XML_PATH_BACKUP_TIME = 'groups/auto_fill/fields/time/value';
    const XML_PATH_BACKUP_FREQUENCY = 'groups/auto_fill/fields/frequency/value';

    /** @var \Magento\Framework\App\Config\ValueFactory */
    private $configValueFactory;

    /** @var string */
    private $runModelPath = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->runModelPath = $runModelPath;
        $this->configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Cron settings after save
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        $enabled = $this->getData(self::XML_PATH_BACKUP_ENABLED);
        $time = $this->getData(self::XML_PATH_BACKUP_TIME);
        $frequency = $this->getData(self::XML_PATH_BACKUP_FREQUENCY);

        $frequencyWeekly = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = [
                (int)($time[1]),                             # Minute
                (int)($time[0]),                             # Hour
                $frequency == $frequencyMonthly ? '1' : '*', # Day of the Month
                '*',                                         # Month of the Year
                $frequency == $frequencyWeekly ? '1' : '*',  # Day of the Week
            ];
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            $this->configValueFactory->create()->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            $this->configValueFactory->create()->load(self::CRON_MODEL_PATH, 'path')
                ->setValue($this->runModelPath)
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the Cron expression.'));
        }

        return parent::afterSave();
    }
}
