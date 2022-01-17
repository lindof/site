<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Field;

use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Day;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Month;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Slots;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Year;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Disabletimeslotdate extends AbstractFieldArray
{

    /**
     * @var Daygroup
     */
    protected $_timeRenderer;
    protected $_dayRenderer;
    protected $_monthRenderer;
    protected $_yearRenderer;
    protected $_day;
    protected $_month;
    protected $_year;
    protected $_timeslot;

    /**
     * @param Context $context
     * @param Day $day
     * @param Month $month
     * @param Year $year
     * @param Slots $timeslot
     * @param array $data
     */
    public function __construct(
        Context $context,
        Day $day,
        Month $month,
        Year $year,
        Slots $timeslot,
        array $data = []
    ) {
        $this->_day = $day;
        $this->_month = $month;
        $this->_year = $year;
        $this->_timeslot = $timeslot;
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render.
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'day',
            ['label' => __('Date'), 'renderer' => $this->_getDayRenderer(), 'style' => 'width:60px']
        );

        $this->addColumn(
            'month',
            ['label' => __('Month'), 'renderer' => $this->_getMonthRenderer(), 'style' => 'width:200px']
        );

        $this->addColumn(
            'year',
            ['label' => __('Year'), 'renderer' => $this->_getYearRenderer(), 'style' => 'width:250px']
        );
        $this->addColumn('time_slot', ['label' => __('Time Slot'), 'renderer' => $this->_getTimeSlotRenderer(), 'style' => 'width:250px' ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Time Slot To Disable');
    }

    /**
     * @return mixed
     */
    protected function _getDayRenderer()
    {
        if (!$this->_dayRenderer) {
            $this->_dayRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_dayRenderer->setClass('day');
            $this->_dayRenderer->setOptions($this->_day->toOptionArray());
        }

        return $this->_dayRenderer;
    }

    /**
     * @return mixed
     */
    protected function _getMonthRenderer()
    {
        if (!$this->_monthRenderer) {
            $this->_monthRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_monthRenderer->setClass('month');
            $this->_monthRenderer->setOptions($this->_month->toOptionArray());
        }

        return $this->_monthRenderer;
    }

    /**
     * @return mixed
     */
    protected function _getYearRenderer()
    {
        if (!$this->_yearRenderer) {
            $this->_yearRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_yearRenderer->setClass('year');
            $this->_yearRenderer->setOptions($this->_year->toOptionArray());
        }

        return $this->_yearRenderer;
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

            $this->_timeRenderer->setClass('timeslots validate-select');
            $this->_timeRenderer->setOptions($this->_timeslot->toOptionArray());
        }

        return $this->_timeRenderer;
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getDayRenderer()->calcOptionHash($row->getData('day'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getMonthRenderer()->calcOptionHash($row->getData('month'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getYearRenderer()->calcOptionHash($row->getData('year'))] = 'selected="selected"';

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
