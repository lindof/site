<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Observer\Adminhtml\CustomerSave;
use Exception;
use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\CustomerFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveSupplierLogo
 * @package Cminds\Marketplace\Observer\Adminhtml\CustomerSave
 */
class SaveSupplierLogo extends CustomerSaveAbstract
{
    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SaveSupplierLogo constructor.
     * @param MarketplaceHelper $marketplaceHelper
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(
        MarketplaceHelper $marketplaceHelper,
        Context $context,
        CustomerFactory $customerFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $marketplaceHelper,
            $context
        );

        $this->marketplaceHelper = $marketplaceHelper;
        $this->customerFactory = $customerFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return $this|bool|void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->marketplaceHelper->canUploadLogos()) {
            return $this;
        }

        $postData = $this->request->getPostValue();
        $customerData = $observer->getCustomer();
        $customerModel = $this->customerFactory->create();
        $path = $this->filesystem->getDirectoryRead(UrlInterface::URL_TYPE_MEDIA)
            ->getAbsolutePath('supplier_logos/');

        if (isset($postData['remove_logo']) && $postData['remove_logo'] == 1) {
            $cData = $this->customerFactory->create()
                ->load($postData['customer_id']);
            $s = $cData->getSupplierLogo();
            if (!is_null($s)) {
                if (file_exists($path . $s)) {
                    unlink($path . $s);
                }
                $customerData->setCustomAttribute('supplier_logo', null);
            } else {
                return $this;
            }
        } else {
            try {
                $this->uploadLogo($customerData);
            } catch (Exception $exception) {
              return false;
            }
        }

        $customerModel->updateData($customerData);
        $customerModel->save();
    }

    /**
     * @param $customerData
     * @throws Exception
     */
    private function uploadLogo($customerData)
    {
        $uploader = $this->uploaderFactory->create(array('fileId' => 'supplier_logo'));
        $file = $uploader->validateFile();

        if (isset($file['name'])) {
            if (file_exists($file['tmp_name'])) {
                $uploader->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'gif',
                    'png',
                ]);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = $this->filesystem->getDirectoryRead(UrlInterface::URL_TYPE_MEDIA)
                    ->getAbsolutePath('supplier_logos/');
                $nameSplit = explode('.', $file['name']);
                $ext = $nameSplit[count($nameSplit) - 1];
                $newName = md5($file['name'] . time()) . '.' . $ext;
                $uploader->save($path, $newName);
                $customerData->setCustomAttribute(
                    'supplier_logo',
                    $newName
                );
            }
        }
    }
}
