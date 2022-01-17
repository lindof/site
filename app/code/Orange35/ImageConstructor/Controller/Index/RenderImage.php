<?php

namespace Orange35\ImageConstructor\Controller\Index;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Orange35\ImageConstructor\Helper\ConfigHelper;
use Orange35\ImageConstructor\Model\Image\Merger;
use Orange35\ImageConstructor\Model\Image\Resizer;
use Orange35\ImageConstructor\Model\Layer;

class RenderImage extends Action
{
    private $resultJsonFactory;
    private $configHelper;
    private $productRepository;
    private $layer;
    private $merger;
    private $resizer;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigHelper $configHelper,
        ProductRepositoryInterface $productRepository,
        Layer $layer,
        Merger $merger,
        Resizer $resizer
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configHelper = $configHelper;
        $this->productRepository = $productRepository;
        $this->layer = $layer;
        $this->merger = $merger;
        $this->resizer = $resizer;
    }

    public function execute()
    {
        $productId = $this->getRequest()->getParam('product');
        $imageId = $this->getRequest()->getParam('image');
        $layersIds = explode('-', $this->getRequest()->getParam('layers'));
        $result = $this->resultJsonFactory->create();
        try {
            if (empty($layersIds)) {
                throw new LocalizedException(__('Layer IDs are not defined'));
            }
            if (!($layers = $this->layer->getById($layersIds))) {
                throw new LocalizedException(__('No Layers found'));
            }

            /** @var Product $product */
            $product = $this->productRepository->getById($productId);
            /** @var Product\Image $image */
            $image = null;
            foreach ($product->getMediaGalleryImages() as $galleryImage) {
                if ($galleryImage->getId() == $imageId) {
                    $image = $galleryImage;
                    break;
                }
            }
            if (!$image) {
                throw new LocalizedException(__('Image not found'));
            }
            $mergePath = $this->configHelper->getMergedImagePath();
            $mergedImage = $this->merger->merge($image->getFile(), $layers, $mergePath);
            $relativeMergedImagePath = $mergePath . $mergedImage;
            $response['image'] = [
                'id' => (int) $imageId
            ];
            $map = $this->configHelper->getLayerImageMap();
            foreach ($map as $property => $imageId) {
                $response['image'][$property] = $this->resizer->getThumbnailUrl($relativeMergedImagePath, $imageId);
            }
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Failed to merge image layers'));
            $response['error'] = $e->getMessage();
            $result->setHttpResponseCode(500);
        }

        $result->setData($response);
        return $result;
    }
}
