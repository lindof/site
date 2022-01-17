<?php

namespace Biztech\Deliverydate\Block\Adminhtml\Config\Form\Field;

use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Day;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Month;
use Biztech\Deliverydate\Model\Adminhtml\System\Config\Source\Year;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Nonworking extends AbstractFieldArray
{
    protected $_dayRenderer = null;
    protected $_monthRenderer = null;
    protected $_yearRenderer = null;
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
        $this->addColumn(
            'sort',
            ['label' => __('Sort'), 'style' => 'width:40px', 'class' => 'required-entry validate-digits']
        );

        $this->addColumn(
            'deliveryday',
            ['label' => __('Day'), 'renderer' => $this->_getDayRenderer()]
        );

        $this->addColumn(
            'deliverymonth',
            ['label' => __('Month'), 'renderer' => $this->_getMonthRenderer()]
        );

        $this->addColumn(
            'deliveryyear',
            ['label' => __('Year'), 'renderer' => $this->_getYearRenderer()]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Non-Working Day');
    }

    /**
     * Retrieve day column renderer.
     *
     * @return Daygroup
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
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getDayRenderer()->calcOptionHash($row->getData('deliveryday'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getMonthRenderer()->calcOptionHash($row->getData('deliverymonth'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getYearRenderer()->calcOptionHash($row->getData('deliveryyear'))] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
