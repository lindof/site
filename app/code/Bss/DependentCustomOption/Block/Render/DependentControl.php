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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Block\Render;

use Bss\DependentCustomOption\Helper\ModuleConfig;
use Bss\DependentCustomOption\Model\DependOptionFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class DependentControl
 *
 * @package Bss\DependentCustomOption\Block\Render
 */
class DependentControl extends Template
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * DependentControl constructor.
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param ModuleConfig $moduleConfig
     * @param DependOptionFactory $dependOptionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        ModuleConfig $moduleConfig,
        DependOptionFactory $dependOptionFactory,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->jsonEncoder = $jsonEncoder;
        $this->dependOptionFactory = $dependOptionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->setTemplate('Bss_DependentCustomOption::select/dependent-control.phtml');
    }

    /**
     * GetConfigHelper
     *
     * @return ModuleConfig
     */
    public function getConfigHelper()
    {
        return $this->moduleConfig;
    }

    /**
     * GetDependData
     *
     * @return bool|string
     */
    public function getDependData()
    {
        $result = [];
        $values = $this->getOption()->getValues();
        $result['dependent_id'] = $this->getOption()->getData('dependent_id');
        foreach ($values as $value) {
            $result['child'][$value->getOptionTypeId()] = $value->getData();
        }
        if ($result['dependent_id']) {
            return $this->jsonEncoder->encode($result);
        }
        return false;
    }
}
