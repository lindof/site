<?php
namespace Act\MainMenu\Block;

use Exception;
use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\CategoryManagementInterface;

class TopMenu extends \Magento\Framework\View\Element\Template 
{
	private $curl;

	private $curlClient;

	/**
	 * @var CategoryManagementInterface
	 */
	private $categoryManagement;	

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		 \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface,
		 \Magento\Framework\HTTP\Adapter\Curl $curl,
		 \Magento\Framework\HTTP\Client\Curl $curlClient,
		 CategoryManagementInterface $categoryManagement)
	{   
		$this->StoreManagerInterface=$StoreManagerInterface;
		$this->curl = $curl;
		$this->curlClient = $curlClient;
		$this->categoryManagement = $categoryManagement;
		parent::__construct($context);
	}
	
	/**
	 * Get category children
	 */
	public function getHats()
	{
		$data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $catId = 15;  //Parent Category ID
    $subCategory = $objectManager->create('Magento\Catalog\Model\Category')->load($catId);
    $children = $subCategory->getChildrenCategories();

		if(!empty($children)){
			foreach($children as $category){
				if(!$category->getIsActive()){
					continue;
				}
				$data[] = [
					'id' => $category->getId(),
					'url' => $category->getUrl(),
					'name' => $category->getName()
				];
			}
		}
		
		return $data;
	}

		/**
	 * Get category children
	 */
	public function getPoms()
	{
		$data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $catId = 16;  //Parent Category ID
    $subCategory = $objectManager->create('Magento\Catalog\Model\Category')->load($catId);
    $children = $subCategory->getChildrenCategories();

		if(!empty($children)){
			foreach($children as $category){
				if(!$category->getIsActive()){
					continue;
				}
				$data[] = [
					'id' => $category->getId(),
					'url' => $category->getUrl(),
					'name' => $category->getName()
				];
			}
		}
		
		return $data;
	}

		/**
	 * Get category children
	 */
	public function getGoldbergh()
	{
		$data = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $catId = 12;  //Parent Category ID
    $subCategory = $objectManager->create('Magento\Catalog\Model\Category')->load($catId);
    $children = $subCategory->getChildrenCategories();

		if(!empty($children)){
			foreach($children as $category){
				if(!$category->getIsActive()){
					continue;
				}
				$data[] = [
					'id' => $category->getId(),
					'url' => $category->getUrl(),
					'name' => $category->getName()
				];
			}
		}
		
		return $data;
	}


	/**
     * Fetch Category Tree
     *
     * @return CategoryTreeInterface
     */
    public function getCategoryTree($rootCategoryId)
    {
				$depth = 1;
        try {
            $categoryTreeList = $this->categoryManagement->getTree($rootCategoryId, $depth);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
				}
				return $categoryTreeList;
    }	

}