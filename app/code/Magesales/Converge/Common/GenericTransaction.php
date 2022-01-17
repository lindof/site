<?php


namespace Magesales\Converge\Common;

use Magesales\ConvergePaymentSpiCommon\Transaction\GenericTransaction as CommonGenericTransaction;

/**
 * Class GenericTransaction
 * This class is a result of a bug https://github.com/magento/magento2/issues/6739
 * and should be removed once the bug is fixed
 *
 * @private
 */
class GenericTransaction extends CommonGenericTransaction
{
    const THIS_CLASS_IS_NOT_EMPTY = 'not_empty';
}
