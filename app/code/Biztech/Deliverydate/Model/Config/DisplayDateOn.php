<?php
namespace Biztech\Deliverydate\Model\Config;

class DisplayDateOn implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $displayOn = [
            [
                'label' => __('All Pages'),
                'value' => [
                    ['label' => __('Display on All Pages'),'value' => 'all'],
                ]
            ],
            [
                'label' => __('Admin Pages'),
                'value' => [
                    ['label' => __('Order Details page (Backend)'), 'value' => 'adminhtml_sales_order_view'],
                    ['label' => __('Invoice Create page (Backend)'), 'value' => 'adminhtml_sales_order_invoice_new'],
                    ['label' => __('Invoice Details page (Backend)'), 'value' => 'adminhtml_sales_order_invoice_view'],
                    ['label' => __('Shipment Create page (Backend)'), 'value' => 'adminhtml_adminhtml_order_shipment_new'],
                    ['label' => __('Shipment Details page (Backend)'), 'value' => 'adminhtml_adminhtml_order_shipment_view'],
                    ['label' => __('Credit Memo Details page Create (Backend)'), 'value' => 'adminhtml_sales_order_creditmemo_new'],
                    ['label' => __('Credit Memo Details page (Backend)'), 'value' => 'adminhtml_sales_order_creditmemo_view'],
                    ['label' => __('Print In Credit Memo (Backend)'), 'value' => 'adminhtml_sales_order_creditmemo_print'],
                    ['label' => __('Print In Shipment (Backend)'), 'value' => 'adminhtml_sales_shipment_print'],
                    ['label' => __('Print In Invoice (Backend)') , 'value' => 'adminhtml_sales_order_invoice_print'],
                ],
            ],
            [
                'label' => __('Frontend Pages'),
                'value' => [
                    ['label' => __('Order Details page (Frontend)') , 'value' => 'frontend_sales_order_view'],
                    ['label' => __('Invoice Details page (Frontend)') , 'value' => 'frontend_sales_order_invoice'],
                    ['label' => __('Shipment Details page (Frontend)') , 'value' => 'frontend_sales_order_shipment'],
                    ['label' => __('Print Order (Frontend)') , 'value' => 'frontend_sales_order_print'],
                    ['label' => __('Print In Invoice (Frontend)') , 'value' => 'frontend_sales_order_printInvoice'],
                    ['label' => __('Print In Shipment (Frontend)') , 'value' => 'frontend_sales_order_printShipment'],
                ]
            ]
        ];
        return $displayOn;
    }
}
