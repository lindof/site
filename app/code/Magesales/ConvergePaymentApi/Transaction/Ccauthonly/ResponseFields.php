<?php


namespace Magesales\ConvergePaymentApi\Transaction\Ccauthonly;

/**
 * Class ResponseFields.
 * @api
 */
class ResponseFields
{
    /**
     * @return string
     */
    const SSL_RESULT = 'ssl_result';

    /**
     * @return string
     */
    const SSL_RECEIPT_LINK_METHOD = 'ssl_receipt_link_method';

    /**
     * @return string
     */
    const SSL_RECEIP_TLINK_URL = 'ssl_receip_tlink_url';

    /**
     * @return string
     */
    const SSL_RECEIPT_LINK_TEXT = 'ssl_receipt_link_text';

    /**
     * @return string
     */
    const SSL_RECEIPT_DECL_METHOD = 'ssl_receipt_decl_method';

    /**
     * @return string
     */
    const SSL_RECEIPT_DECL_GET_URL = 'ssl_receipt_decl_get_url';

    /**
     * @return string
     */
    const SSL_RECEIPT_DECL_POST_URL = 'ssl_receipt_decl_post_url';

    /**
     * @return string
     */
    const SSL_RECEIPT_DECL_TEXT = 'ssl_receipt_decl_text';

    /**
     * @return string
     */
    const SSL_RECEIPT_APPRVL_METHOD = 'ssl_receipt_apprvl_method';

    /**
     * @return string
     */
    const SSL_RECEIPT_APPRVL_GET_URL = 'ssl_receipt_apprvl_get_url';

    /**
     * @return string
     */
    const SSL_RECEIPT_APPRVL_POST_URL = 'ssl_receipt_apprvl_post_url';

    /**
     * @return string
     */
    const SSL_RECEIPT_APPRVL_LINK_TEXT = 'ssl_receipt_apprvl_link_text';
}
