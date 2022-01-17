<?php


namespace Magesales\Converge\Gateway\Logger;

use Magento\Framework\Logger\Handler\System;

/**
 * Class Critical
 */
class Critical extends System
{
    const DEFAULT_SYSTEM_FILE_NAME = '/var/log/converge_system.log';

    /**
     * @var string
     */
    protected $fileName = self::DEFAULT_SYSTEM_FILE_NAME;
}
