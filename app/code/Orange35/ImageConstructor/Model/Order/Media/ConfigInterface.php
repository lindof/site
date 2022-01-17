<?php

namespace Orange35\ImageConstructor\Model\Order\Media;

interface ConfigInterface
{
    public function getBaseMediaPath();

    public function getBaseMediaUrl();

    public function getLayerBasePath();

    public function getBaseMediaThumbnailPath();

    public function getBaseMediaThumbnailUrl();
}
