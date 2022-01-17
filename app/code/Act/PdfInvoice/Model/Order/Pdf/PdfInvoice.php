<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Act\PdfInvoice\Model\Order\Pdf;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PdfInvoice extends Invoice {

    /**
     * @var \Swissup\CheckoutFields\Helper\Data
     */
    protected $swissUpHelper;

    /**
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param Emulation $appEmulation
     * @param \Swissup\CheckoutFields\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem, Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        Emulation $appEmulation,
        \Swissup\CheckoutFields\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $storeManager, $appEmulation, $data);
        $this->swissUpHelper =  $helper;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $pdf  = parent::getPdf($invoices);
        $page = &$pdf->pages[0];
        $y    = 20;

        foreach ($invoices as $invoice) {
            $checkoutFields = $this->swissUpHelper->getOrderFieldsValues($invoice->getOrder(), ['comments']);
            if (count($checkoutFields)) {
                foreach ($checkoutFields as $field) {
                    $orderComment = $field->getValue();
                    $page->drawText($orderComment, 20, $y);
                    $y += 10;
                }
            }
        }
        $y += 15;
        $page->drawText('Comments:', 20, $y);

        // Append Gift message Field
        foreach ($invoices as $invoice) {
            $checkoutFields = $this->swissUpHelper->getOrderFieldsValues($invoice->getOrder(), ['gift_message']);
            if (count($checkoutFields)) {
                foreach ($checkoutFields as $field) {
                    $orderComment = $field->getValue();
                    $page->drawText($orderComment, 300, $y);
                    $y += 10;
                }
            }
        }
        $y += 15;
        $page->drawText('Gift Message:', 300, $y);


        return $pdf;
    }
}
