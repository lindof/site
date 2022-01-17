<?php


namespace Magesales\Converge\Common;

use Magesales\ConvergePaymentSpiCommon\ValidationService as CommonValidationService;

/**
 * Class ValidationService
 * This class is a result of a bug https://github.com/magento/magento2/issues/6739
 * and should be removed once the bug is fixed
 *
 * @private
 */
class ValidationService extends CommonValidationService
{
    const THIS_CLASS_IS_NOT_EMPTY = 'not_empty';
}
