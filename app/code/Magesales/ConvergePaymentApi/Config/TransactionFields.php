<?php

namespace Magesales\ConvergePaymentApi\Config;

/**
 * Interface TransactionFields.
 * @api
 */
interface TransactionFields
{
    const TRANSACTION_TYPE = 'ssl_transaction_type';

    /**
     * Converge ID as provided by Elavon
     *
     * @var string
     */
    const SSL_MERCHANT_ID = 'ssl_merchant_id';

    /**
     * Converge user ID as configured on Converge
     *
     * (case sensitive)
     *
     * @var string
     */
    const SSL_USER_ID = 'ssl_user_id';

    /**
     * Converge PIN as generated within Converge
     *
     * (case sensitive)
     *
     * @var string
     */
    const SSL_PIN = 'ssl_pin';

    /**
     * Transaction Type
     *
     * @var string
     */
    const SSL_TRANSACTION_TYPE = 'ssl_transaction_type';

    /**
     * Show Form Flag
     *
     * Used for customized payment form
     *
     * @var bool|null
     */
    const SSL_SHOW_FORM = 'ssl_show_form';

    /**
     * Transaction Sale Amount
     *
     * Used for customized payment form. If set then it is unable to be changed on the Converge payment form
     *
     * @var float|null
     */
    const SSL_AMOUNT = 'ssl_amount';

    /**
     * Card Number
     *
     * Used for customized payment form. Rrequired for hand-keyed transactions where the track data is not present.
     *
     * @var string|null
     */
    const SSL_CARD_NUMBER = 'ssl_card_number';

    /**
     * Credit Card Token (previously generated from a card number).
     * Required for hand-keyed transaction if card number is not sent.
     *
     * @var string|null
     */
    const SSL_TOKEN = 'ssl_token';

    /**
     * Expiration Date (formatted as MMYY).
     * Used for customized payment form. Required to be used with card number on hand-keyed transactions.
     *
     * @var string|null
     */
    const SSL_EXP_DATE = 'ssl_exp_date';

    /**
     * Cardholder ZIP code.
     *
     * @var string|null
     */
    const SSL_AVS_ZIP = 'ssl_avs_zip';

    /**
     * Cardholder address.
     *
     * @var string|null
     */
    const SSL_AVS_ADDRESS = 'ssl_avs_address';

    /**
     * The credit card security code.
     *
     * @var string|null
     */
    const SSL_CVV2CVC2 = 'ssl_cvv2cvc2';

    /**
     * The CVV2/CVC/CID indicator should be passed with the credit card security code (CVV).
     * Indicates if the CVV is present in the request.
     * - 0 - for Bypassed.
     * - 1 - for Present.
     * - 2 - for Illegible.
     * - 9 - for Not Present.
     *
     * @var string|null
     */
    const SSL_CVV2CVC2_INDICATOR = 'ssl_cvv2cvc2_indicator';

    /**
     * The description, merchant defined value.
     *
     * @var string|null
     */
    const SSL_DESCRIPTION = 'ssl_description';

    /**
     * The Customer Code or PO Number that appears on the cardholder's credit card billing statement.
     *
     * @var string|null
     */
    const SSL_CUSTOMER_CODE = 'ssl_customer_code';

    /**
     * Sales tax amount applied to this transaction in decimal.
     * Tax exempt transactions can pass 0.00 to properly reflect a tax exempt transaction.
     *
     * @var float|null
     */
    const SSL_SALESTAX = 'ssl_salestax';

    /**
     * Shift, can refer to or be used to identify time period, course or type of service.
     * Alphanumeric, Example: Lunch.
     *
     * @var string|null
     */
    const SSL_SHIFT = 'ssl_shift';

    /**
     * DBA Name provided by the merchant with each transaction.
     * The maximum allowable Length of DBA Name variable provided by Merchant can be 21, 17 or 12
     * based on the length setup for the DBA constant in the field setup.
     *
     * var string|null
     */
    const SSL_DYNAMIC_DBA = 'ssl_dynamic_dba';

    /**
     * The partial indicator flag must be sent to indicate that
     * the application supports partial approval.
     * - 0 – Partial Auth not supported
     * - 1 – Partial Auth supported
     *
     * @var int|null
     */
    const SSL_PARTIAL_AUTH_INDICATOR = 'ssl_partial_auth_indicator';

    /**
     * The recurring flag must be sent to indicate if a credit card
     * sale transaction is a recurring or an installment payment.
     * Use only when maintaining your own recurring and installment database.
     * When the flag is indicated as Installment, the payment number and payment count must be passed.
     *
     * - 1 - Recurring
     * - 2 - Installment
     *
     * @var int|null
     */
    const SSL_RECURRING_FLAG = 'ssl_recurring_flag';

    /**
     * Installment Sequence Number (Payment Number).
     *
     * @var string|null
     */
    const SSL_PAYMENT_NUMBER = 'ssl_payment_number';

    /**
     * Installment Count (total number of payments).
     *
     * @var string|null
     */
    const SSL_PAYMENT_COUNT = 'ssl_payment_count';

    /**
     * Date of the departure.
     * Format MM/DD/YYYY.
     * This field will be sent to the MARMS system to monitor risk associated with advanced booking.
     * Examples include airlines and travel or tour agencies.
     * Only used when setup with option for MARMS.
     *
     * @var string|null
     */
    const SSL_DEPARTURE_DATE = 'ssl_departure_Date';

    /**
     * Date of the completion of travel.
     * Format MM/DD/YYYY.
     * This field is sent to the MARMS system to monitor risk associated with advanced booking.
     * Examples include airlines and travel or tour agencies.
     * Only used when setup with Travel Data option for MARMS.
     *
     * @var string|null
     */
    const SSL_COMPLETION_DATE = 'ssl_completion_Date';

    /**
     * Transaction currency alphanumeric code.
     * Must be included in the request to indicate the currency in which you wish to process.
     * If omitted, the default currency is assumed (for example: USD, CAD, and JPY).
     *
     * @var string|null
     */
    const SSL_TRANSACTION_CURRENCY = 'ssl_transaction_currency';

    /**
     * Generate Token indicator.
     * Used to indicate if you wish to generate a token when passing card data.
     * - Y - Generate a token.
     * - N - Do not generate token (default).
     *
     * @var string|null
     */
    const SSL_GET_TOKEN = 'ssl_get_token';

    /**
     * Add to Card Manager indicator.
     * Used to indicate if you wish to generate a token and store it in Card Manager.
     * - Y - Add token.
     * - N - Do not add token (default).
     *
     * @var string|null
     */
    const SSL_ADD_TOKEN = 'ssl_add_token';

    /**
     * Customer's address line 2.
     *
     * @var string|null
     */
    const SSL_ADDRESS2 = 'ssl_address2';

    /**
     * Cardholder first name.
     * Required with Add to Card Manager indicator when generating a toke from card number.
     *
     * @var string|null
     */
    const SSL_FIRST_NAME = 'ssl_first_name';

    /**
     * Cardholder last name.
     * Required with Add to Card Manager indicator when generating a toke from card number.
     *
     * @var string|null
     */
    const SSL_LAST_NAME = 'ssl_last_name';

    /**
     * Customer's city.
     *
     * @var string|null
     */
    const SSL_CITY = 'ssl_city';

    /**
     * Customer's state.
     *
     * @var string|null
     */
    const SSL_STATE = 'ssl_state';

    /**
     * Customer's country.
     *
     * @var string|null
     */
    const SSL_COUNTRY = 'ssl_country';

    /**
     * Customer's phone.
     *
     * @var string|null
     */
    const SSL_PHONE = 'ssl_phone';

    /**
     * Customer's email.
     *
     * @var string|null
     */
    const SSL_EMAIL = 'ssl_email';

    /**
     * Ship To company name.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_COMPANY = 'ssl_ship_to_company';

    /**
     * Ship To first name.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_FIRST_NAME = 'ssl_ship_to_first_name';

    /**
     * Ship To last name.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_LAST_NAME = 'ssl_ship_to_last_name';

    /**
     * Ship To address line 1.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_ADDRESS1 = 'ssl_ship_to_address1';

    /**
     * Ship To address line 2.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_ADDRESS2 = 'ssl_ship_to_address2';

    /**
     * Ship To city.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_CITY = 'ssl_ship_to_city';

    /**
     * Ship To state.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_STATE = 'ssl_ship_to_state';

    /**
     * Ship To ZIP code.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_ZIP = 'ssl_ship_to_zip';

    /**
     * Ship To country.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_COUNTRY = 'ssl_ship_to_country';

    /**
     * Ship To phone number.
     *
     * @var string|null
     */
    const SSL_SHIP_TO_PHONE = 'ssl_ship_to_phone';

    /**
     * Receipt result format.
     * - HTML - HTML receipt will be returned (default).
     * - XML - XML receipt will be returned.
     * - ASCII - Only a list of key value pairs will be returned.
     *
     * @var string|null
     */
    const SSL_RESULT_FORMAT = 'ssl_result_format';

    /**
     * Receipt link method.
     * - GET - Customer will be returned back to website (data will be transmitted with GET parameters).
     * - POST - Customer will be returned back to website (data will be transmitted with POST parameters).
     * - LINK - Customer will be returned back to website (data wont be transmitted).
     * - REDG - Customer will be redirected back to website (receipt will be displayed of our own).
     *
     * @return string|null
     */
    const SSL_RECEIPT_LINK_METHOD = 'ssl_receipt_link_method';

    /**
     * Receipt link URL.
     * Converge gives the option to send approved and declined transactions to the same URL
     * or to different URLs to handle them separately.
     *
     * @return string|null
     */
    const SSL_RECEIPT_LINK_URL = 'ssl_receipt_link_url';

    /**
     * Receipt link text.
     *
     * @return string|null
     */
    const SSL_RECEIPT_LINK_TEXT = 'ssl_receipt_link_text';

    /**
     * Receipt form header.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_HEADER_HTML = 'ssl_receipt_header_html';

    /**
     * Receipt form footer.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_FOOTER_HTML = 'ssl_receipt_footer_html';

    /**
     * Receipt approval method.
     * - GET -  Receipt displayed. Button provided to send GET data to ssl_receipt_link_url.
     * - POST - Receipt displayed. Button provided to send POST data to ssl_receipt_link_url.
     * - LINK - Receipt displayed. Link provided to return to ssl_receipt_link_url.
     * - REDG - No receipt displayed. Data redirected to ssl_receipt_link_url.
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_METHOD = 'ssl_receipt_apprvl_method';

    /**
     * Target of the link used for GET and REDG approval methods.
     * Ignored when ssl_result_format = ASCII and ssl_receipt_link_method = LINK or POST
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_GET_URL = 'ssl_receipt_apprvl_get_url';

    /**
     * Target of the link generated at the bottom of the receipt for an approval using the POST method.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_POST_URL = 'ssl_receipt_apprvl_post_url';

    /**
     * Text that appears on the receipts of approved transactions.
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_LINK_TEXT = 'ssl_receipt_apprvl_link_text';

    /**
     * Receipt form header for approved transaction.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_HEADER_HTML = 'ssl_receipt_apprvl_header_html';

    /**
     * Receipt form footer for approved transaction.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_APPRVL_FOOTER_HTML = 'ssl_receipt_apprvl_footer_html';

    /**
     * Receipt decline method.
     * - GET -  Receipt displayed. Button provided to send GET data to ssl_receipt_link_url.
     * - POST - Receipt displayed. Button provided to send POST data to ssl_receipt_link_url.
     * - LINK - Receipt displayed. Link provided to return to ssl_receipt_link_url.
     * - REDG - No receipt displayed. Data redirected to ssl_receipt_link_url.
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_METHOD = 'ssl_receipt_decl_method';

    /**
     * Target of the link used for GET and REDG decline methods.
     * Ignored when ssl_result_format = ASCII and ssl_receipt_link_method = LINK or POST
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_GET_URL = 'ssl_receipt_decl_get_url';

    /**
     * Target of the link generated at the bottom of the receipt for an decline using the POST method.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_POST_URL = 'ssl_receipt_decl_post_url';

    /**
     * Text that appears on the receipts of declined transactions.
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_TEXT = 'ssl_receipt_decl_text';

    /**
     * Receipt form header for declined transaction.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_HEADER_HTML = 'ssl_receipt_decl_header_html';

    /**
     * Receipt form footer for declined transaction.
     * Ignored when ssl_result_format = ASCII.
     *
     * @return string|null
     */
    const SSL_RECEIPT_DECL_FOOTER_HTML = 'ssl_receipt_decl_footer_html';

    /**
     * Error redirect URL.
     * If present, the error will be redirected to the URL specified
     * including the errorCode, errorName and errorMessage fields.
     *
     * @return string|null
     */
    const SSL_ERROR_URL = 'ssl_error_url';

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // POS related fields (not used)

    /**
     * The payment application capability.
     * Used for customized payment form. Required to be used with card number on hand-keyed transactions.
     * - 02 - for Swiped device. Default value when track Data is sent alone.
     * - 03 - Proximity / Contactless capable device.
     *
     * @var string|null
     */
    const SSL_POS_MODE = 'ssl_pos_mode';

    /**
     * The transaction entry indicator to indicate how the track data was captured.
     * - 03 - Swiped. Default value when track Data is sent alone.
     * - 04 - Proximity / Contactless.
     *
     * @var string|null
     */
    const SSL_ENTRY_MODE = 'ssl_entry_mode';

    /**
     * Card track data.
     * The raw track I and/or II data from the magnetic strip on the card.
     *
     * @var string|null
     */
    const SSL_TRACK_DATA = 'ssl_track_data';

    /**
     * Card encrypted track data.
     * Should be passed if using an Ingenico encrypting device.
     *
     * @var string|null
     */
    const SSL_ENC_TRACK_DATA = 'ssl_enc_track_data';

    /**
     * Card encrypted track data.
     * Should be passed if using a Magtek encrypting device.
     *
     * @var string|null
     */
    const SSL_ENCRYPTED_TRACK1_DATA = 'ssl_encrypted_track1_data';

    /**
     * Card encrypted track data
     *
     * Should be passed if using a Magtek encrypting device.
     *
     * @var string|null
     */
    const SSL_ENCRYPTED_TRACK2_DATA = 'ssl_encrypted_track2_data';

    /**
     * Key Serial Number generated from the swiped payment card
     *
     * Required when sending track data from an encrypting device for payment cards.
     *
     * @var string|null
     */
    const SSL_KSN = 'ssl_ksn';

    /**
     * Indicates if the card was present at the time of the transaction
     *
     * - Y - Card present.
     * - N - Card is not present.
     *
     * @var string|null
     */
    const SSL_CARD_PRESENT = 'ssl_card_present';

    /**
     * Tip or gratuity amount to be added
     *
     * 0.00 will reset or remove the original tip from a transaction.
     *  Example: 1.00.
     *
     * @var float|null
     */
    const SSL_TIP_AMOUNT = 'ssl_tip_amount';

    /**
     * Server ID, this is the clerk, cashier, and waiter or waitress identification number
     *
     * Alphanumeric, Example: Jack
     *
     * @var string|null
     */
    const SSL_SERVER = 'ssl_server';

    /**
     * Test Mode. 'true' or 'false' values
     *
     * @var string
     */
    const SSL_TEST_MODE = 'ssl_test_mode';
}
