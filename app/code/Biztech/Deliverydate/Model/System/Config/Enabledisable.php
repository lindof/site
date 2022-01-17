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

namespace Biztech\Deliverydate\Model\System\Config;

use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Option\ArrayInterface;

class Enabledisable implements ArrayInterface
{

    protected $_helper;

    public function __construct(
        ObjectManagerInterface $interface,
        Data $helperdata
    ) {
        $this->objectManager = $interface;
        $this->_helper = $helperdata;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 0, 'label' => __('No')],
        ];
        $websites = $this->_helper->getAllWebsites();
        if (!empty($websites)) {
            $options[] = ['value' => 1, 'label' => __('Yes')];
        }

        return $options;
    }
}
