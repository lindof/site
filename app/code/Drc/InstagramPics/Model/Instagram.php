<?php

namespace Drc\InstagramPics\Model;

//use Magento\Backend\Block\Template\Context;
use Drc\InstagramPics\Helper\Config;
use Drc\InstagramPics\Model\Instagram\Api;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Exception;
use Magento\Framework\Data\Collection\EntityFactoryInterface;

class Instagram
{
    protected $_api = null;
    protected $_configHelper;
    protected $_logger;
    protected $_entityFactoryInterface;

    public function __construct(
        Config $configHelper,
        Api $api,
        Monolog $logger,
        EntityFactoryInterface $entityFactoryInterface
        //Context $context,
        //array $data = []
    ) {
        $this->_configHelper = $configHelper;
        $this->_api = $api;
        $this->_entityFactoryInterface = $entityFactoryInterface;
        $this->_logger = $logger;
        //parent::__construct($context,$data);
    }

    public function getApi()
    {
        if ($this->_api === null) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $object = $objectManager->create(
                'Drc\InstagramPics\Model\Instagram\Api',
                $this->_configHelper->getAccessToken()
            );
            $this->_api = $object;
        }
        return $this->_api;
    }

    /**
     * @param string $name
     * @param int $limit
     *
     * @return Varien_Data_Collection
     */
    public function getTagMedia($name, $limit = 0)
    {
        $collection = new \Magento\Framework\DataObject();
        try {
            $response = $this->getApi()->getTagMedia($name, $limit);
        //} catch (\Magento\Framework\Exception $e) {
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return $collection;
        }
        if (!isset($response->data) || !is_array($response->data)) {
            return $collection;
        }
        foreach ($response->data as $item) {
            if (!isset($item->images->low_resolution->url)) {
                continue;
            }
            $image = new \Magento\Framework\Object();
            $image->setUrl($item->images->low_resolution->url);
            if (isset($item->caption->text)) {
                $image->setName($item->caption->text);
            }
            if (isset($item->link)) {
                $image->setLink($item->link);
            }
            $collection->addItem($image);
        }
        return $collection;
    }

    /**
     * @param string $userId
     * @param int    $limit
     *
     * @return Varien_Data_Collection
     */
    public function getUserMediaById($userId, $limit = 0)
    {
        //$collection = new \Magento\Framework\DataObject();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Framework\Data\Collection');
        try {
            $response = $this->getApi()->getUserMedia($userId, $limit);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return $collection;
        }
        if (!isset($response->data) || !is_array($response->data)) {
            return $collection;
        }
        foreach ($response->data as $item) {
            if (!isset($item->images->low_resolution->url)) {
                continue;
            }
            //$image = new Varien_Object();
            //$image = new \Magento\Framework\Object();
            $image = $objectManager->create('Magento\Framework\DataObject');
            //$image = $this->_entityFactoryInterface;
            
            $image->setUrl($item->images->low_resolution->url);
            if (isset($item->caption->text)) {
                $image->setName($item->caption->text);
            }
            if (isset($item->link)) {
                $image->setLink($item->link);
            }
            $collection->addItem($image);
        }
        return $collection;
    }

    /**
     * @param string $userName
     * @param int $limit
     *
     * @return Varien_Data_Collection
     */
    public function getUserMediaByName($userName, $limit = 0)
    {
        $collection = new \Magento\Framework\DataObject();
        try {
            $response = $this->getApi()->searchUser($userName);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return $collection;
        }
        if (!isset($response->data) || !is_array($response->data) || !count($response->data)) {
            return $collection;
        }
        $user = current($response->data);
        return $this->getUserMediaById($user->id, $limit);
    }
}
