<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Biztech\Deliverydate\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Widget\Tabs;

class Deliverydetails extends Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Biztech_Deliverydate::dashboard/deliverydetails.phtml');
    }
}
