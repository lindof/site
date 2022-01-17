<?php

namespace Biztech\Deliverydate\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category as categoryHelper;

class Category implements ArrayInterface
{
    protected $_categoryHelper;
    protected $categoryRepository;
    protected $categoryList;
    protected $_storeManager;
    protected $request;

    public function __construct(
        categoryHelper $catalogCategory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_categoryHelper = $catalogCategory;
        $this->categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;
        $this->request = $request;
    }

    /*
     * Return categories helper
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $website = $this->request->getParam('website', 0);
        if ($website !== 0) {
            $storeids = $this->_storeManager->getStoreByWebsiteId($website);
            if (isset($storeids)) {
                $storeId = $storeids[0];
                $this->_storeManager->setCurrentStore($storeId);
            }
        } else {
            $storeId = $this->request->getParam('store', 0);
            if ($storeId !== 0) {
                $this->_storeManager->setCurrentStore($storeId);
            }
        }
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $ret[] = [
                    'value' => $key,
                    'label' => $value
                ];
            }
        } else {
            $ret[] = [
                'value' => '',
                'label' => __("No Category's available")
            ];
        }
        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $categories = $this->getStoreCategories(true, false, true);
        $categoryList = $this->renderCategories($categories);
        return $categoryList;
    }

    public function renderCategories($_categories)
    {
        foreach ($_categories as $category) {
            $i = 0;
            $this->categoryList[$category->getEntityId()] = __($category->getName());   // Main categories
            $list = $this->renderSubCat($category, $i);
        }

        return $this->categoryList;
    }

    public function renderSubCat($cat, $j)
    {
        $categoryObj = $this->categoryRepository->get($cat->getId());

        $level = $categoryObj->getLevel();
        $arrow = str_repeat("--- ", $level - 1);
        $subcategories = $categoryObj->getChildrenCategories();

        foreach ($subcategories as $subcategory) {
            $this->categoryList[$subcategory->getEntityId()] = __($arrow . $subcategory->getName());

            if ($subcategory->hasChildren()) {
                $this->renderSubCat($subcategory, $j);
            }
        }

        return $this->categoryList;
    }
}
