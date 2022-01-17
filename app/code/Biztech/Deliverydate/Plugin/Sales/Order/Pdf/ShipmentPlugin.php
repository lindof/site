<?php

namespace Biztech\Deliverydate\Plugin\Sales\Order\Pdf;

/**
 * Class InvoicePlugin
 */
class ShipmentPlugin
{

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $invoices
     * @return mixed
     */
    public function aroundGetPdf($subject, \Closure $proceed, $invoices)
    {
        foreach ($invoices as $invoice) {
            $invoice->getOrder()->setAppendDeliveryDate(true);
        }
        $returnValue = $proceed($invoices);

        return $returnValue;
    }
}
