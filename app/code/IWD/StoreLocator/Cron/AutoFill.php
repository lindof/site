<?php

namespace IWD\StoreLocator\Cron;

use IWD\StoreLocator\Model\GeoLocation;
use Psr\Log\LoggerInterface as PsrLogger;

class AutoFill
{
    private $geoLocation;

    private $logger;

    public function __construct(
        GeoLocation $geoLocation,
        PsrLogger $logger)
    {
        $this->geoLocation = $geoLocation;
        $this->logger = $logger;
    }

    /**
     * Auto-fill geo-location via cron
     */
    public function execute()
    {
        try {
            $this->geoLocation->fillGeoData();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
