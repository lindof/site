<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Field;

use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Starttime;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Timeslot extends AbstractFieldArray
{
    protected $_starttimeRenderer;
    protected $_endtimeRenderer;
    protected $_time;

    /**
     * @param Context $context
     * @param Starttime $time
     * @param array $data
     */
    public function __construct(
        Context $context,
        Starttime $time,
        array $data = []
    ) {
        $this->_time = $time;
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render.
     */
    protected function _prepareToRender()
    {
        $this->addColumn('sort_period', ['label' => __('Sort'), 'style' => 'width:50px', 'class' => 'required-entry validate-digits']);
        $this->addColumn('start_time', ['label' => __('Start Time'), 'renderer' => $this->_getStartTimeRenderer(), 'style' => 'width:100px']);
        $this->addColumn('end_time', ['label' => __('End Time'), 'renderer' => $this->_getEndTimeRenderer(), 'style' => 'width:100px']);

        $this->addColumn('price', ['label' => __('price'), 'style' => 'width:100px', 'class' => 'validate-number']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Time Slot');
    }

    /**
     * Retrieve start column renderer.
     *
     * @return StartTimegroup
     */
    protected function _getStartTimeRenderer()
    {
        if (!$this->_starttimeRenderer) {
            $this->_starttimeRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_starttimeRenderer->setClass('timeslots');
            $this->_starttimeRenderer->setStyle('width:115px');
            $this->_starttimeRenderer->setOptions($this->_time->toOptionArray('23:30'));
        }

        return $this->_starttimeRenderer;
    }

    protected function _getEndTimeRenderer()
    {
        if (!$this->_endtimeRenderer) {
            $this->_endtimeRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_endtimeRenderer->setClass('timeslots');
            $this->_endtimeRenderer->setStyle('width:115px');
            $this->_endtimeRenderer->setOptions($this->_time->toOptionArray('00:00'));
        }

        return $this->_endtimeRenderer;
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getStartTimeRenderer()->calcOptionHash($row->getData('start_time'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getEndTimeRenderer()->calcOptionHash($row->getData('end_time'))] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
