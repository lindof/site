<?php


namespace Magesales\Converge\Gateway\Logger;

use Magento\Framework\Logger\Handler\Debug as FrameworkDebug;

/**
 * Class Debug
 */
class Debug extends FrameworkDebug
{
    const DEFAULT_DEBUG_FILE_NAME = '/var/log/converge_debug.log';

    /**
     * @var string
     */
    protected $fileName = self::DEFAULT_DEBUG_FILE_NAME;
}
