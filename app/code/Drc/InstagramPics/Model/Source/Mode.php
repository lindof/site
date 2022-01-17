<?php

namespace Drc\InstagramPics\Model\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    const BY_USER_ID_CODE          = 1;
    const BY_USER_ID_LABEL         = 'By User ID';

    const BY_HASHTAG_CODE          = 2;
    const BY_HASHTAG_LABEL         = 'By Hashtag';

    const BY_PRODUCT_HASHTAG_CODE  = 3;
    const BY_PRODUCT_HASHTAG_LABEL = 'By Product Hashtag';

    const BY_USER_NAME_CODE        = 4;
    const BY_USER_NAME_LABEL       = 'By User Name';

    public function toOptionArray()
    {
        return [
                ['value' => self::BY_USER_ID_CODE,         'label' => __(self::BY_USER_ID_LABEL)],
                ['value' => self::BY_HASHTAG_CODE,         'label' => __(self::BY_HASHTAG_LABEL)],
                //['value' => self::BY_PRODUCT_HASHTAG_CODE, 'label' => __(self::BY_PRODUCT_HASHTAG_LABEL)],
                ['value' => self::BY_USER_NAME_CODE,       'label' => __(self::BY_USER_NAME_LABEL)]
        ];
    }
}
