<?php


namespace Magesales\ConvergePaymentSpi;

/**
 * Interface HttpClient.
 * @api
 */
interface HttpClient
{
    /**
     * Places request to gateway
     *
     * @param array $data
     * @return array
     */
    public function placeRequest(array $data);
}
