<?php
namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

/**
 * Order infoSpinandwintion tab
 *
 * @author      Knowband Team
 */
use Magento\Backend\Block\Widget\Grid\Column;

class UserList extends \Magento\Backend\Block\Widget\Grid\Extended implements
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
        return __('Return Statuses');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Return Statuses');
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
        \Knowband\Spinandwin\Model\Coupons $couponModel,
        \Knowband\Spinandwin\Model\Users $userModel,
        \Knowband\Spinandwin\Model\Email $emailModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->sp_emailModel = $emailModel;
        $this->sp_couponModel = $couponModel;
        $this->sp_userModel = $userModel;
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
        $this->setId('vss_spin_user_list');
        $this->setDefaultSort('id_user_list');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        $this->setVarNameFilter('coupon_code');
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_userModel->getCollection();
        $userTable = $collection->getTable('vss_spinandwin_coupons');
        $collection->getSelect()->joinLeft(array('coupon' => $userTable), "(main_table.coupon_id = coupon.coupon_id)");
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('id_user_list', array(
            'header' => '#',
            'index' => 'id_user_list',
            'filter' => false
        ));
        
        $this->addColumn('coupon_code', array(
            'header' => __('Coupon Code'),
            'index' => 'coupon_code',
            'sortable' => false
        ));
        
        $this->addColumn('customer_email', array(
            'header' => __('Customer Email'),
            'index' => 'customer_email',
            'sortable' => false
        ));

        $this->addColumn('country', array(
            'header' => __('Country'),
            'index' => 'country',
            'sortable' => false
        ));

        $this->addColumn('device', array(
            'header' => __('Device'),
            'index' => 'device',
            'sortable' => false
        ));
        
        $this->addColumn('coupon_usage', array(
            'header' => __('Coupon Used'),
            'index' => 'coupon_usage',
            'sortable' => false,
            'type' => 'options',
            'options' => array(
                '0' => __('No'),
                '1' => __('Yes')
            )
//            'renderer' => 'Knowband\Spinandwin\Block\Adminhtml\CouponUsage'
        ));

        $this->addColumn('coupon_created_at', array(
            'header' => __('Coupon Generated On'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'created_at',
            'filter_index' => 'coupon.created_at'
        ));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('spinwinadmin/spinandwin/userlistajax', ['_current' => true]);
    }
    
}
