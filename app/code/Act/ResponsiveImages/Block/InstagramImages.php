<?php
namespace Act\ResponsiveImages\Block;

class InstagramImages extends \Magento\Framework\View\Element\Template 
{
	private $curl;

	private $curlClient;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		 \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface,
		 \Magento\Framework\HTTP\Adapter\Curl $curl,
		 \Magento\Framework\HTTP\Client\Curl $curlClient)
	{   
		$this->StoreManagerInterface=$StoreManagerInterface;
		$this->curl = $curl;
		$this->curlClient = $curlClient;
		parent::__construct($context);
	}
       
	public function getMedia()
	{
		$images = [];
		$uri = "https://api.instagram.com/v1/users/self/media/recent/?access_token=1565065936.1677ed0.8627a252b5ac4a198f66f25c093ad172&count=12";
		$lindof_token = '1565065936.1677ed0.8627a252b5ac4a198f66f25c093ad172';
		
		// Use this site to generate a new token
		// https://instagram.pixelunion.net/

		$this->curlClient->get($uri);

		$result = $this->curlClient->getBody();
    if (!$result) {
      return [];
		}
		
		return $result;

		$result = json_decode($result, TRUE);
    foreach ($result['data'] as $post) {
      $images[$post['id']] = [
        'href' => $post['link'],
        'src' => $post['images']['low_resolution']['url'],
        'width' => 300,
        'height' => 300,
      ];
		}

		return $images;
	}

}