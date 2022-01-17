<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;

class ScheduleActions extends AbstractRenderer {

    public function render(DataObject $row) {
           
        $data = $row->getData();
        $html = '';
        $editScheduleUrl = $this->getUrl('*/*/addSchedule', ['schedule_id' => $data['schedule_id']]);
         $html .= '<button type="button" class="btn btn-success" onclick="location.href = \''.$editScheduleUrl.'\'">' . __('Edit') . '</button>';
        
        $deleteScheduleUrl = $this->getUrl('*/*/deleteSchedule', ['schedule_id' => $data['schedule_id']]);
        $html .= '<button type="button" class="btn btn-danger" onclick="location.href = \''.$deleteScheduleUrl.'\'">' . __('Delete') . '</button>';
        
        return $html;
        
    }

}
