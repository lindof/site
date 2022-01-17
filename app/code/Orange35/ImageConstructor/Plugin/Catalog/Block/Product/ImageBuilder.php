<?php

namespace Orange35\ImageConstructor\Plugin\Catalog\Block\Product;

class ImageBuilder
{
    public function afterCreate(\Magento\Catalog\Block\Product\ImageBuilder $subject, $result)
    {
        if ($result instanceof \Magento\Catalog\Block\Product\Image) {
            //echo $result->getTemplate(); exit;
            $template = $result->getTemplate();
            $template = str_replace('Magento_Catalog::', 'Orange35_ImageConstructor::', $template);
            //echo $template; exit;
            $result->setTemplate($template);
        }
        return $result;
    }
}
