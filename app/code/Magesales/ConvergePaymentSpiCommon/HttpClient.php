<?php


namespace Magesales\ConvergePaymentSpiCommon;

use Magesales\ConvergePaymentApi\Config;
use Magesales\ConvergePaymentSpi;
use Magesales\ConvergePaymentApi\Config\TransactionFields;
use Zend_Http_Client;

/**
 * Class HttpClient.
 * @private
 */
class HttpClient implements ConvergePaymentSpi\HttpClient
{
    /**
     * @var Config
     */
    private $config;

    /**
     * HttpClient constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function placeRequest(array $data)
    {
        $result = [];
        try {
            // add/enforce required parameters (ssl_merchant_id, ssl_pin, ssl_show_form)
            $data[TransactionFields::SSL_MERCHANT_ID] = $this->config->getMerchantId();
            $data[TransactionFields::SSL_USER_ID] = $this->config->getUserId();
            $data[TransactionFields::SSL_PIN] = $this->config->getPin();
            $data[TransactionFields::SSL_SHOW_FORM] = $this->config->getFormType() === 'converge';
            $data[TransactionFields::SSL_RESULT_FORMAT] = 'ASCII';

            $client = new Zend_Http_Client();
            $client->setMethod($this->config->getHttpClientMethod());
            $client->setConfig($this->config->getHttpClientConfig());

            switch ($this->config->getHttpClientMethod()) {
                case \Zend_Http_Client::GET:
                    $client->setParameterGet($data);
                    break;
                case \Zend_Http_Client::POST:
                    $client->setParameterPost($data);
                    break;
                default:
                    throw new \LogicException(
                        sprintf(
                            'Unsupported HTTP method %s',
                            $this->config->getHttpClientMethod()
                        )
                    );
            }

            $client->setHeaders($this->config->getHttpClientHeaders());
            $client->setUri($this->config->getGatewayUrl());

            $response = $client->request();

            $result = $this->convert($response->getBody());
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Converts gateway response to array structure.
     *
     * @param array $response
     * @return array
     */
    private function convert($response)
    {
        $response = (array) $response;
        foreach ($response as $key => $value) {
            if (is_object($value)) {
                $response[$key] = $this->convert($value);
            }
        }

        return $response;
    }
}
