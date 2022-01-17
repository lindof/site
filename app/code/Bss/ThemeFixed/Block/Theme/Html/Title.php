<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ThemeFixed
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ThemeFixed\Block\Theme\Html;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;

/**
 * Class Title
 * @package Bss\ThemeFixed\Block\Theme\Html
 */
class Title extends \Magento\Theme\Block\Html\Title
{
    /**
     * Const
     */
    const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param ScopeCoUndefinednfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = [],
        Http $request,
        Registry $registry
    ) {
        parent::__construct($context, $scopeConfig, $data);
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }

        $pageTitle = $this->pageConfig->getTitle()->getShort();

        return $this->shouldTranslateTitle() ? __($pageTitle) : $pageTitle;
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Check if page title should be translated
     *
     * @return bool
     */
    private function shouldTranslateTitle()
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_HEADER_TRANSLATE_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        $pageTitle = !empty($this->pageTitle) ? $this->pageTitle : $this->pageConfig->getTitle()->getShortHeading();
        return $this->shouldTranslateTitle() ? __($pageTitle) : $pageTitle;
    }

    public function testing(){
        $currentProduct = $this->registry->registry('current_product');
        if ($this->request->getFullActionName() == 'catalog_product_view' && $currentProduct ) {
            //you are on the product page
            
            return $currentProduct->getName();
            //return "product page";
        }
       

        return $subject->getPageHeading();
        
    }
}
