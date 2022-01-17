<?php

namespace IWD\StoreLocator\Controller;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Router
 * @package IWD\StoreLocator\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    private $response;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $status = $this->scopeConfig->getValue('iwd_storelocator/general/is_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$status) {
            return null;
        }

        $locatorRoute = $this->scopeConfig->getValue('iwd_storelocator/general/path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $locatorRoute = strtolower($locatorRoute);
        $locatorRoute = trim($locatorRoute);

        $identifier = trim($request->getPathInfo(), '/');
        $action = $request->getActionName();
        if ($request->getModuleName() === 'storelocator' && $request->getControllerName() == 'index') {
            return;
        }
        if ($identifier == $locatorRoute) {
            $request->setModuleName('storelocator')
                ->setControllerName('index')
                ->setActionName($action);
        } else {
            //There is no match
            return null;
        }

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
