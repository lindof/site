<?php


namespace Magesales\Converge\Api;

/**
 * Interface GuestPaymentManagementInterface
 * @api
 * @deprecated since 1.1.0
 */
interface GuestPaymentManagementInterface
{
    /**
     * @param string $cartId
     * @return bool
     */
    public function restoreQuote($cartId);
}
