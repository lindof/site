<?php


namespace Magesales\Converge\Common;

use Magesales\ConvergePaymentSpiCommon\TransactionService as CommonTransactionService;

/**
 * Class TransactionService
 * This class is a result of a bug https://github.com/magento/magento2/issues/6739
 * and should be removed once the bug is fixed
 *
 * @private
 */
class TransactionService extends CommonTransactionService
{
    const THIS_CLASS_IS_NOT_EMPTY = 'not_empty';
}
