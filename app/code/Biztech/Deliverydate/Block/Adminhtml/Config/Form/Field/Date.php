<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Field;

use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Day;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Month;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Year;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Date extends AbstractFieldArray
{

    /**
     * @var Daygroup
     */
    protected $_fromDayRenderer;
    protected $_fromMonthRenderer;
    protected $_FromyearRenderer;
    protected $_TodayRenderer;
    protected $_TomonthRenderer;
    protected $_ToyearRenderer;
    protected $_day;
    protected $_month;
    protected $_year;

    /**
     * @param Context $context
     * @param Day $day
     * @param Month $month
     * @param Year $year
     * @param array $data
     */
    public function __construct(
        Context $context,
        Day $day,
        Month $month,
        Year $year,
        array $data = []
    ) {
        $this->_day = $day;
        $this->_month = $month;
        $this->_year = $year;
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render.
     */
    protected function _prepareToRender()
    {
        $this->addColumn('sort_period', ['label' => __('Sort '), 'style' => 'width: 40px', 'class' => 'required-entry validate-digits']);

        $this->addColumn(
            'from_day',
            ['label' => __('From Date'), 'renderer' => $this->_getFromDayRenderer()]
        );

        $this->addColumn(
            'from_month',
            ['label' => __('From Month'), 'renderer' => $this->_getFromMonthRenderer()]
        );

        $this->addColumn(
            'from_year',
            ['label' => __('From Year'), 'renderer' => $this->_getFromYearRenderer()]
        );

        $this->addColumn(
            'to_day',
            ['label' => __('To Date'), 'renderer' => $this->_getToDayRenderer()]
        );

        $this->addColumn(
            'to_month',
            ['label' => __('To Month'), 'renderer' => $this->_getToMonthRenderer()]
        );

        $this->addColumn(
            'to_year',
            ['label' => __('To Year'), 'renderer' => $this->_getToYearRenderer()]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Non-Working Period');
    }

    /**
     * Retrieve day column renderer.
     *
     * @return Daygroup
     */
    protected function _getFromDayRenderer()
    {
        if (!$this->_fromDayRenderer) {
            $this->_fromDayRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_fromDayRenderer->setClass('from-day');
            $this->_fromDayRenderer->setOptions($this->_day->toOptionArray());
        }

        return $this->_fromDayRenderer;
    }

    /**
     * @return mixed
     */
    protected function _getFromMonthRenderer()
    {
        if (!$this->_fromMonthRenderer) {
            $this->_fromMonthRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_fromMonthRenderer->setClass('from-month');
            $this->_fromMonthRenderer->setOptions($this->_month->toOptionArray());
        }

        return $this->_fromMonthRenderer;
    }

    /**
     * @return mixed
     */
    protected function _getFromYearRenderer()
    {
        if (!$this->_FromyearRenderer) {
            $this->_FromyearRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_FromyearRenderer->setClass('from-year');
            $this->_FromyearRenderer->setOptions($this->_year->toOptionArray());
        }

        return $this->_FromyearRenderer;
    }

    protected function _getToDayRenderer()
    {
        if (!$this->_TodayRenderer) {
            $this->_TodayRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_TodayRenderer->setClass('to-day');
            $this->_TodayRenderer->setOptions($this->_day->toOptionArray());
        }

        return $this->_TodayRenderer;
    }

    protected function _getToMonthRenderer()
    {
        if (!$this->_TomonthRenderer) {
            $this->_TomonthRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_TomonthRenderer->setClass('to-month');
            $this->_TomonthRenderer->setOptions($this->_month->toOptionArray());
        }

        return $this->_TomonthRenderer;
    }

    protected function _getToYearRenderer()
    {
        if (!$this->_ToyearRenderer) {
            $this->_ToyearRenderer = $this->getLayout()->createBlock(
                'Biztech\Deliverydate\Block\Adminhtml\Config\Form\Renderer\Ddselect',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_ToyearRenderer->setClass('to-year');
            $this->_ToyearRenderer->setOptions($this->_year->toOptionArray());
        }

        return $this->_ToyearRenderer;
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getFromDayRenderer()->calcOptionHash($row->getData('from_day'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getFromMonthRenderer()->calcOptionHash($row->getData('from_month'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getFromYearRenderer()->calcOptionHash($row->getData('from_year'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getToDayRenderer()->calcOptionHash($row->getData('to_day'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getToMonthRenderer()->calcOptionHash($row->getData('to_month'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getToYearRenderer()->calcOptionHash($row->getData('to_year'))] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
