<?php

namespace Drc\InstagramPics\Model\System\Config\Backend;

use Magento\Config\Model\Config;

class EmptyInsta extends Config
{
    public function getValue()
    {
        return null;
    }
}
