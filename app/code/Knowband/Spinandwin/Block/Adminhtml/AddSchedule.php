<?php
/**
* Added by Bhupendra Singh Bisht
* To add the Theme Schedule.         
*/
namespace Knowband\Spinandwin\Block\Adminhtml;

class AddSchedule extends \Magento\Backend\Block\Template
{
    const DEFAULT_SECTION_BLOCK = 'Magento\Config\Block\System\Config\Form';
    private $sp_helper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Spinandwin\Helper\Data $helper,
        \Knowband\Spinandwin\Model\ThemeSchedule $themeSchedule,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_themeSchedule = $themeSchedule;
        parent::__construct($context, $data);
    }
    
     protected function _prepareLayout()
    {
        $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        $this->getToolbar()->addChild(
            'save_button', 'Magento\Backend\Block\Widget\Button', [
                'id' => 'save-schedule',
                'label' => __('Save Schedule'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#spinandwin_schedule_view']],
                ]
            ]
        );
        
        $this->getToolbar()->addChild(
            'back_button', 'Magento\Backend\Block\Widget\Button', [
                'id' => 'back',
                'label' => __('Back'),
                'class' => 'action- scalable back',
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#spinandwin_schedule_view']],
                ]
            ]
        );
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }
    

  
    public function getSettings($key)
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }
    
    public function getThemes()
    {
        
       return $this->sp_helper->getThemes();
    }    
    
    public function getWheelDesign()
    {
        return $this->sp_helper->getWheelDesign();
    }
    
    public function getScheduleSettings($schedule_id)
    {
        $scheduleData = [];
        if($schedule_id){
            $scheduleModel = $this->sp_themeSchedule->load($schedule_id);
            $scheduleSettings = $scheduleModel->getSettings();
            $scheduleData = json_decode($scheduleSettings, true);
        }
        
        return $scheduleData;
    }
    
}
