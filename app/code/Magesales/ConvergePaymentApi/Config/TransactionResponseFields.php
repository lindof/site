<?php


namespace Magesales\ConvergePaymentApi\Config;

/**
 * Interface TransactionResponseFields.
 * @api
 */
interface TransactionResponseFields
{
    /**
     * Transaction identification number. This is a unique number used to identify the transaction.
     * @var string
     */
    const SSL_TXN_ID = 'ssl_txn_id';

    const SSL_TXN_TIME = 'ssl_txn_time';

    const SSL_APPROVAL_CODE = 'ssl_approval_code';

    const SSL_APPROVAL_MESSAGE = 'ssl_approval_message';
    const SSL_RESULT_MESSAGE = 'ssl_result_message';
    const SSL_CARD_SHORT_DESCRIPTION = 'ssl_card_short_description';
    const SSL_CVV2_RESPONSE = 'ssl_cvv2_response';
    const SSL_AVS_RESPONSE = 'ssl_avs_response';
    const SSL_DEPARTURE_DATE = 'ssl_departure_date';
    const SSL_COMPLETION_DATE = 'ssl_completion_date';
    const SSL_INVOICE_NUMBER = 'ssl_invoice_number';

    /**
     * Result format.
     * - 0 - Represents an approved transaction.
     * - [error code] - Represents a declined transaction or a transaction that had an error.
     *
     * @return string
     */
    const SSL_RESULT = 'ssl_result';

    /**
     * ...
     * - GET - Customer will be returned back to website (data will be transmitted with GET parameters).
     * - POST - Customer will be returned back to website (data will be transmitted with POST parameters).
     * - LINK - Customer will be returned back to website (data wont be transmitted).
     * - REDG - Customer will be redirected back to website (receipt will be displayed of our own).
     *
     * @return string
     */
    const SSL_RECEIPT_LINK_METHOD = 'ssl_receipt_link_method';

    /**
     * ...
     * Converge gives the option to send approved and declined transactions to the same URL
     * or to different URLs to handle them separately.
     *
     * @return string
     */
    const SSL_RECEIP_TLINK_URL = 'ssl_receip_tlink_url';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_LINK_TEXT = 'ssl_receipt_link_text';

    // Variables below allow for a different type of receipt for approvals
    // and declines. If you use the variables above, they will take precedence over the following parameters.

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_DECL_METHOD = 'ssl_receipt_decl_method';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_DECL_GET_URL = 'ssl_receipt_decl_get_url';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_DECL_POST_URL = 'ssl_receipt_decl_post_url';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_DECL_TEXT = 'ssl_receipt_decl_text';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_APPRVL_METHOD = 'ssl_receipt_apprvl_method';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_APPRVL_GET_URL = 'ssl_receipt_apprvl_get_url';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_APPRVL_POST_URL = 'ssl_receipt_apprvl_post_url';

    /**
     * ...
     *
     * @return string
     */
    const SSL_RECEIPT_APPRVL_LINK_TEXT = 'ssl_receipt_apprvl_link_text';
}
