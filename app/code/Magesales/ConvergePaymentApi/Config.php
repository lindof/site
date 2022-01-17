<?php


namespace Magesales\ConvergePaymentApi;

/**
 * Interface Config
 *
 * @api
 */
interface Config
{
    /**
     * Get Payment configuration status
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get Converge Merchant ID
     *
     * @return string
     */
    public function getMerchantId();

    /**
     * Get Converge User ID.
     * @return string
     */
    public function getUserId();

    /**
     * Get Converge Merchant PIN.
     * @return string
     */
    public function getPin();

    /**
     * Get Form Type
     *
     * @return string
     */
    public function getFormType();

    /**
     * Get payment action
     *
     * @return string
     */
    public function getPaymentAction();

    /**
     * Get transaction type
     *
     * @return string
     */
    public function getTransactionType();

    /**
     * Get current active mode gateway URL
     *
     * @return string
     */
    public function getGatewayUrl();

    /**
     * HTTP Client request method
     *
     * @return string
     */
    public function getHttpClientMethod();

    /**
     * HTTP Client configuration additional settings
     *
     * @return array
     */
    public function getHttpClientConfig();

    /**
     * HTTP Client special headers
     *
     * @return array
     */
    public function getHttpClientHeaders();

    /**
     * Operation mode
     *
     * @return string
     */
    public function getMode();

    /**
     * @return bool
     */
    public function shouldEncodeRequestBody();

    /**
     * @return string
     */
    public function getModuleVersion();

    /**
     * @return string
     */
    public function getReceiptLinkText();

    /**
     * @return string
     */
    public function getReceiptApprovalUri();
}
