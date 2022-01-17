<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Field;

use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Slots;
use Biztech\Deliverydate\Model\Config\Dayoff;
use Magento\Backend\Block\Template\Context;

class Disabletimeslot extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_timeRenderer;
    protected $_dayoffRenderer;
    protected $_timeslot;
    protected $_dayoff;

    /**
     * @param Context $context
     * @param Slots $timeslot
     * @param Dayoff $dayoff
     * @param array $data
     */
    public function __construct(
        Context $context,
        Slots $timeslot,
        Dayoff $dayoff,
        array $data = []
    ) {
        $this->_timeslot = $timeslot;
        $this->_dayoff = $dayoff;
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render.
     */
    protected function _prepareToRender()
    {
        $this->addColumn('day', ['label' => __('Day'), 'renderer' => $this->_getDayOffRenderer(), 'style' => 'width:194px']);
        $this->addColumn('time_slot', ['label' => __('Time Slot'), 'renderer' => $this->_getTimeSlotRenderer(), 'style' => 'width:194px']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Time Slot To Disable');
    }

    protected function _getDayOffRenderer()
    {
        if (!$this->_dayoffRenderer) {
            $this->_dayoffRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_dayoffRenderer->setClass('dayoff');
            $this->_dayoffRenderer->setStyle('width:115px;');
            $this->_dayoffRenderer->setOptions($this->_dayoff->toDays());
        }

        return $this->_dayoffRenderer;
    }

    /**
     * Retrieve Timeslot column renderer.
     *
     * @return timeslotgroup
     */
    protected function _getTimeSlotRenderer()
    {
        if (!$this->_timeRenderer) {
            $this->_timeRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddsmultiselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_timeRenderer->setClass('disabletimeslot validate-select');
            $this->_timeRenderer->setOptions($this->_timeslot->toOptionArray());
        }

        return $this->_timeRenderer;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getDayOffRenderer()->calcOptionHash($row->getData('day'))] = 'selected="selected"';
        $timeSlots = $row->getData('time_slot');
        if (isset($timeSlots)) {
            foreach ($timeSlots as $key => $value) {
                $optionExtraAttr['option_' . $this->_getTimeSlotRenderer()->calcOptionHash($value)] = 'selected="selected"';
            }
        }
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
