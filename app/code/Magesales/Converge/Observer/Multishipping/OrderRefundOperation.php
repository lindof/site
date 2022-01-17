<?php


namespace Magesales\Converge\Observer\Multishipping;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * Class OrderRefundOperation
 */
class OrderRefundOperation
{
    /**
     * @var CreditmemoManagementInterface
     */
    private $creditmemoManagement;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * OrderRefundOperation constructor.
     * @param CreditmemoManagementInterface $creditmemoManagement
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        CreditmemoManagementInterface $creditmemoManagement,
        CreditmemoRepositoryInterface $creditmemoRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->creditmemoManagement = $creditmemoManagement;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function refund(OrderInterface $order)
    {
        if (!$order->canCreditmemo()) {
            return false;
        }

        $creditmemo = $this->creditmemoRepository->create();
        $creditmemo->setOrderId($order->getEntityId());

        $invoices = $this->getInvoices($order);
        if (!empty($invoices)) {
            $invoice = reset($invoices);
            $creditmemo->setInvoiceId($invoice->getEntityId());
        }
        $this->creditmemoManagement->refund($creditmemo, false);
        return true;
    }

    /**
     * @param OrderInterface $order
     * @return InvoiceInterface[]
     */
    private function getInvoices(OrderInterface $order)
    {
        $orderIdFilter = $this->filterBuilder
            ->setField(InvoiceInterface::ORDER_ID)
            ->setValue($order->getEntityId())
            ->setConditionType('eq')
            ->create();

        $transactionIdFilter = $this->filterBuilder
            ->setField(InvoiceInterface::TRANSACTION_ID)
            ->setValue($order->getPayment()->getLastTransId())
            ->setConditionType('eq')
            ->create();

        $invoiceSearchCriteria = $this->searchCriteriaBuilder
            ->addFilter($orderIdFilter)
            ->addFilter($transactionIdFilter)
            ->create();

        /** @var InvoiceInterface[] $invoices */
        return $this->invoiceRepository->getList($invoiceSearchCriteria)->getItems();
    }
}
