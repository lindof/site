<?php

namespace Biztech\Deliverydate\Plugin\Sales\Order\Pdf;

/**
 * Class CreditmemoPlugin
 */
class CreditmemoPlugin
{

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $invoices
     * @return mixed
     */
    public function aroundGetPdf($subject, \Closure $proceed, $creditmemos)
    {
        foreach ($creditmemos as $creditmemo) {
            $creditmemo->getOrder()->setAppendDeliveryDate(true);
        }
        $returnValue = $proceed($creditmemos);

        return $returnValue;
    }
}
