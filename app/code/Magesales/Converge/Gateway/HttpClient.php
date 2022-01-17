<?php


namespace Magesales\Converge\Gateway;

use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magesales\ConvergePaymentApi\Config;
use Magesales\ConvergePaymentSpi;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Payment\Model\Method\Logger;

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
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * HttpClient constructor.
     * @param Config $config
     * @param ZendClientFactory $clientFactory
     * @param Logger $logger
     * @param ConverterInterface|null $converter
     */
    public function __construct(
        Config $config,
        ZendClientFactory $clientFactory,
        Logger $logger,
        ConverterInterface $converter = null
    ) {
        $this->config = $config;
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
        $this->converter = $converter;
    }

    /**
     * @inheritDoc
     */
    public function placeRequest(array $data)
    {
        $result = [];
        try {
            /** @var ZendClient $client */
            $client = $this->clientFactory->create();

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

            $client->setMethod($this->config->getHttpClientMethod());
            $client->setConfig($this->config->getHttpClientConfig());
            $client->setHeaders($this->config->getHttpClientHeaders());
            $client->setUrlEncodeBody($this->config->shouldEncodeRequestBody());
            $client->setUri($this->config->getGatewayUrl());

            $response = $client->request();

            $result = $this->converter ? $this->converter->convert($response->getBody()) : $response->getBody();
            $result = is_array($result) ? $result : [$result];
        } catch (\Exception $exception) {
            $this->logger->debug(['error' => $exception->getMessage()]);
            throw new ClientException(__('An error occurred while sending request. Please try again later.'));
        } finally {
            $this->logger->debug([
                'request_url' => $client->getUri(),
                'request' => $data,
                'response' => $result
            ]);
        }

        return $result;
    }
}
