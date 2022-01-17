<?php

namespace Cminds\Supplierfrontendproductuploader\Ui\Customer\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class DataProviderWithDefaultAddresses
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(RequestInterface $request, UrlInterface $urlBuilder)
    {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function afterGetConfigData(\Magento\Ui\DataProvider\AbstractDataProvider $subject, $result)
    {
        if ($this->request->getParam('supplier', false) && isset($result['submit_url'])) {
            $result['submit_url'] = $this->urlBuilder->getUrl('*/*/save', ['supplier' => 1]);
        }

        return $result;
    }
}
