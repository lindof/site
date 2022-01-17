<?php 

namespace MageSpark\Productname\Block\CatalogWidget\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\Builder as SqlBuilder;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;
use Magento\Catalog\Model\Layer\Category\CollectionFilter;
use Magento\Framework\Registry;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
	/**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var EncoderInterface|null
     */
    private $urlEncoder;

    /**
     * @var RendererList
     */
    private $rendererListBlock;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;


    private $categoryObject;
    private $collectionFilter;
    private $categoryProductsCollection;
    private $coreRegistry;


    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        HttpContext $httpContext,
        SqlBuilder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        CollectionFilter $collectionFilter,
        Registry $coreRegistry,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        CategoryRepositoryInterface $categoryRepository = null
    ) {
    	$this->collectionFilter = $collectionFilter;
    	$this->coreRegistry = $coreRegistry;
        $this->categoryProductsCollection = ObjectManager::getInstance()->create(\elasticsearchCategoryCollection::class);
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        $this->layoutFactory = $layoutFactory ?: ObjectManager::getInstance()->get(LayoutFactory::class);
        $this->urlEncoder = $urlEncoder ?: ObjectManager::getInstance()->get(EncoderInterface::class);
        $this->categoryRepository = $categoryRepository ?? ObjectManager::getInstance()
                ->get(CategoryRepositoryInterface::class);

        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data, $json, $layoutFactory, $urlEncoder, $categoryRepository);
    }

    public function createCollection()
    {
    	$curCat = $this->coreRegistry->registry('current_category');
        $this->getConditions();

        if($this->categoryObject != null) {
        	if($curCat) $this->coreRegistry->unregister('current_category');
        	$this->coreRegistry->register('current_category', $this->categoryObject);
        	$collection = $this->categoryProductsCollection;
        	$collection->addCategoryFilter($this->categoryObject);
        	ObjectManager::getInstance()->create(\Magento\Catalog\Model\Layer\Category\CollectionFilter::class)->filter($collection, $this->categoryObject);
        } else {
        	$collection = $this->productCollectionFactory->create();
        }

        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }

        /**
         * Change sorting attribute to entity_id because created_at can be the same for products fastly created
         * one by one and sorting by created_at is indeterministic in this case.
         */
        $collection
            ->setPageSize($this->getPageSize())
        	->setOrder('entity_id')
        	->addAttributeToSort('position','ASC')
        	->load();

        $this->coreRegistry->unregister('current_category');
        $this->coreRegistry->register('current_category', $curCat);
        return $collection;
    }

    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])) {
                if ($condition['attribute'] == 'category_ids') {
                    // $conditions[$key] = $this->updateAnchorCategoryConditions($condition);
                	if (array_key_exists('value', $condition)) {
			            $categoryId = $condition['value'];

			            try {
			                $this->categoryObject = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
			            } catch (NoSuchEntityException $e) {}
			        }
                    unset($conditions[$key]);
                }
            }
        }

        return $this;
    }
}