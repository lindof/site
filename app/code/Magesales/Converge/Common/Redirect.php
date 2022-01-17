<?php


namespace Magesales\Converge\Common;

use Magesales\Converge\Api\RedirectInterface;
use Magesales\Converge\Gateway\Config\Config;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

/**
 * Class Redirect
 * @deprecated since 1.1.0
 */
class Redirect implements RedirectInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Session
     */
    private $sessionManager;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Redirect constructor
     *
     * @param Session $session
     * @param CommandPoolInterface $commandPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $session,
        CommandPoolInterface $commandPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->sessionManager = $session;
        $this->commandPool = $commandPool;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getRedirectUrl()
    {
        $order = $this->sessionManager->getLastRealOrder();
        if (!$order->getId()) {
            throw new LocalizedException(__('Order does not exist.'));
        }

        try {
            $commandSubject = [
                'payment' => $this->paymentDataObjectFactory->create($order->getPayment())
            ];
            $response = $this->commandPool->get('redirect')->execute($commandSubject);

            return json_encode($response->get());
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Something went wrong while processing redirect. Please try again later.'));
        }
    }
}
