<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark_Productname
 * @author    MageSpark team
 * @copyright 2021 MageSpark
*/
namespace MageSpark\Productname\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Theme\Block\Html\Title;

/**
 * Class Productname
 * @package MageSpark\Productname\Plugin
 * Replace SEO product title to original product title
 */
class Productname
{
    /**
     * Config path to 'Translate Title' header settings
     */
    const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';

    /**
     * @var Http
     */
    protected $request;


    /**
     * @var Registry
     */
    protected $registry;

    /**
     *
     * @param Http $request
     * @param Registry $registry
     */
    public function __construct(
        Http $request,
        Registry $registry
    ) {
        $this->request      = $request;
        $this->registry     = $registry;
    }

    /**
     * @param Title $subject
     */
    public function afterGetPageTitle(Title $subject)
    {   
        
        if ($this->request->getFullActionName() == 'catalog_product_view') {
            $currentProduct = $this->registry->registry('current_product');
            return $currentProduct->getName();
        }
    }
}
