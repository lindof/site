<?php


namespace Magesales\Converge\Gateway\Config;

use Magesales\ConvergePaymentApi\Config as ConvergeConfigInterface;

/**
 * Class Config
 */
class Config extends CommonConfig implements ConvergeConfigInterface
{
    const MODULE_NAME = 'Magesales_Converge';

    const KEY_ACTIVE = 'active';
    const KEY_PAYMENT_ACTION = 'payment_action';
    const KEY_TRANSACTION_TYPE = 'transaction_type';

    const KEY_MODE = 'mode';

    const PRODUCTION_GATEWAY_URL = 'production_gateway_url';
    const DEMO_GATEWAY_URL = 'demo_gateway_url';

    const PRODUCTION_KEY_MERCHANT_ID = 'production_merchant_id';
    const DEMO_KEY_MERCHANT_ID = 'demo_merchant_id';

    const PRODUCTION_KEY_USER_ID = 'production_user_id';
    const DEMO_KEY_USER_ID = 'demo_user_id';

    const PRODUCTION_KEY_PIN = 'production_pin';
    const DEMO_KEY_PIN = 'demo_pin';

    const KEY_FORM_TYPE = 'form_type';

    const KEY_HTTP_METHOD = 'http_method';
    const KEY_HTTP_ENCODED = 'http_encoded';

    const KEY_RECEIPT_LINK_TEXT = 'receipt_link_text';
    const KEY_RECEIPT_APPROVAL_URI = 'receipt_approval_uri';

    const MODE_DEMO = 'demo';
    const MODE_PRODUCTION = 'production';

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function getGatewayUrl()
    {
        if ($this->getValue(self::KEY_MODE) == 'demo') {
            return $this->getValue(self::DEMO_GATEWAY_URL);
        } else {
            return $this->getValue(self::PRODUCTION_GATEWAY_URL);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMerchantId()
    {
        if ($this->getValue(self::KEY_MODE) == 'demo') {
            return $this->getValue(self::DEMO_KEY_MERCHANT_ID);
        } else {
            return $this->getValue(self::PRODUCTION_KEY_MERCHANT_ID);
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        if ($this->getValue(self::KEY_MODE) == 'demo') {
            return $this->getValue(self::DEMO_KEY_USER_ID);
        } else {
            return $this->getValue(self::PRODUCTION_KEY_USER_ID);
        }
    }

    /**
     * @inheritDoc
     */
    public function getPin()
    {
        if ($this->getValue(self::KEY_MODE) == 'demo') {
            return $this->getValue(self::DEMO_KEY_PIN);
        } else {
            return $this->getValue(self::PRODUCTION_KEY_PIN);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFormType()
    {
        return $this->getValue(self::KEY_FORM_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionType()
    {
        return $this->getValue(self::KEY_TRANSACTION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentAction()
    {
        return $this->getValue(self::KEY_PAYMENT_ACTION);
    }

    /**
     * @inheritDoc
     */
    public function getHttpClientMethod()
    {
        return $this->getValue(self::KEY_HTTP_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function getHttpClientConfig()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getHttpClientHeaders()
    {
        $headers = trim($this->getValue(self::KEY_HTTP_METHOD));
        return $headers ? explode(';', $headers) : [];
    }

    /**
     * @inheritDoc
     */
    public function getMode()
    {
        return $this->getValue(self::KEY_MODE);
    }

    /**
     * @inheritDoc
     */
    public function shouldEncodeRequestBody()
    {
        return $this->getValue(self::KEY_HTTP_ENCODED);
    }

    /**
     * @deprecated since 1.0.6
     * @return string
     */
    public function getModuleVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getReceiptLinkText()
    {
        return $this->getValue(self::KEY_RECEIPT_LINK_TEXT);
    }

    /**
     * @return string
     */
    public function getReceiptApprovalUri()
    {
        return $this->getValue(self::KEY_RECEIPT_APPROVAL_URI);
    }
}
