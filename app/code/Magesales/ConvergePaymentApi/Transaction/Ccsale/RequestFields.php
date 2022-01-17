<?php


namespace Magesales\ConvergePaymentApi\Transaction\Ccsale;

/**
 * Class RequestFields.
 * @api
 *
 */
class RequestFields
{
    /**
     * @var string
     */
    const SSL_TRANSACTION_TYPE = 'ssl_transaction_type';

    /**
     * @var string
     */
    const SSL_MERCHANT_ID = 'ssl_merchant_id';

    /**
     * @var string
     */
    const SSL_USER_ID = 'ssl_user_id';

    /**
     * @var string
     */
    const SSL_PIN = 'ssl_pin';

    /**
     * @var string
     */
    const SSL_SHOW_FORM = 'ssl_show_form';

    /**
     * @var string
     */
    const SSL_TOKEN = 'ssl_token';

    /**
     * @var string
     */
    const SSL_CARD_NUMBER = 'ssl_card_number';

    /**
     * @var string
     */
    const SSL_EXP_DATE = 'ssl_exp_date';

    /**
     * @var string
     */
    const SSL_AMOUNT = 'ssl_amount';

    /**
     * @var string
     */
    const SSL_AVS_ZIP = 'ssl_avs_zip';

    /**
     * @var string
     */
    const SSL_AVS_ADDRESS = 'ssl_avs_address';

    /**
     * @var string
     */
    const SSL_CVV2CVC2 = 'ssl_cvv2cvc2';

    /**
     * @var string
     */
    const SSL_INVOICE_NUMBER = 'ssl_invoice_number';

    /**
     * @var string
     */
    const SSL_DESCRIPTION = 'ssl_description';

    /**
     * @var string
     */
    const SSL_RECURRING_FLAG = 'ssl_recurring_flag';

    /**
     * @var string
     */
    const SSL_PAYMENT_NUMBER = 'ssl_payment_number';

    /**
     * @var string
     */
    const SSL_PAYMENT_COUNT = 'ssl_payment_count';

    /**
     * @var string
     */
    const SSL_TRANSACTION_CURRENCY = 'ssl_transaction_currency';

    /**
     * @var string
     */
    const SSL_GET_TOKEN = 'ssl_get_token';

    /**
     * @var string
     */
    const SSL_ADD_TOKEN = 'ssl_add_token';
}
