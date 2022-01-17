<?php
namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

/**
 * Order infoSpinandwintion tab
 *
 * @author      Knowband Team
 */
use Magento\Backend\Block\Widget\Grid\Column;

class SpinUserlist extends \Magento\Backend\Block\Widget\Grid\Extended implements
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
        
        \Knowband\Spinandwin\Model\SpinUserData $spinuserdata,
        
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->sp_spinUserdata = $spinuserdata;
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
        $this->setId('vss_spin_user_data');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
//        $this->setVarNameFilter('coupon_code');
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_spinUserdata->getCollection();
//        $userTable = $collection->getTable('vss_spinandwin_coupons');
//        $collection->getSelect()->join(array('coupon' => $userTable), "(main_table.coupon_id = coupon.coupon_id)");
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('id', array(
            'header' => '#',
            'index' => 'id',
            'filter' => false
        ));
        
        $this->addColumn('fname', array(
            'header' => __('First Name'),
            'index' => 'fname',
            'sortable' => false
        ));
        
        $this->addColumn('lname', array(
            'header' => __('Last Name'),
            'index' => 'lname',
            'sortable' => false
        ));

        $this->addColumn('email', array(
            'header' => __('Email'),
            'index' => 'email',
            'sortable' => false
        ));
        
        $this->addColumn('date_added', array(
            'header' => __('Created Date'),
            'index' => 'date_added',
            'type' => 'datetime',
            'sortable' => false
        ));

        $this->addExportType($this->getUrl("*/*/exportExcel"),__('CSV'));
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('spinwinadmin/spinandwin/spinandwinuserlistajax', ['_current' => true]);
    }
    
}
