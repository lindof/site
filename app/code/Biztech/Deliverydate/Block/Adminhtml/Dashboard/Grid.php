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

use Magento\Backend\Block\Widget\Grid\Extended;

/**
 * Adminhtml dashboard grid.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends Extended
{

    /**
     * @var string
     */
    protected $_template = 'Biztech_Deliverydate::dashboard/grid.phtml';

    /**
     * Setting default for every grid on dashboard.
     */
    protected function _construct()
    {
        parent::_construct();

        //$this->setDefaultLimit(5);
    }
}
