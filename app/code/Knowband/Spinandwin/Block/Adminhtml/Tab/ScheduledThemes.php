<?php
namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

/**
 * Order infoSpinandwintion tab
 *
 * @author      Knowband Team
 */
use Magento\Backend\Block\Widget\Grid\Column;

class ScheduledThemes extends \Magento\Backend\Block\Widget\Grid\Extended implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_coreRegistry = null;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;
    
    
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('User List');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('User List');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,        
        \Knowband\Spinandwin\Model\ThemeSchedule $themeScheduleModel,        
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_themeScheduleModel = $themeScheduleModel;
        $this->_coreRegistry = $coreRegistry;
        $this->_resource = $resource;
        
        parent::__construct($context, $backendHelper, $data);
    }

  
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('vss_spin_theme_scheduling');
        $this->setDefaultSort('schedule_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_themeScheduleModel->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('schedule_id', array(
            'header' => 'Schedule Id',
            'index' => 'schedule_id',
        ));
        
         $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                '1' => __('Enabled'),
                '0' => __('Disabled')
            )
        ));
        
        $this->addColumn('from_date', array(
            'header' => __('From Date'),
            'type' => 'datetime',
            'index' => 'from_date',
        ));
        
        $this->addColumn('to_date', array(
            'header' => __('To Date'),
            'type' => 'datetime',
            'index' => 'to_date',
        ));
        
        $this->addColumn('action', [
            'header' => __('Action'),
            'type' => 'action',
            'width' => '50px',           
            'renderer' => 'Knowband\Spinandwin\Block\Adminhtml\Renderer\ScheduleActions',
            'filter' => false,
            'sortable' => false
        ]);
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('spinwinadmin/spinandwin/themeSchedulingAjax', ['_current' => true]);
    }
    
}
