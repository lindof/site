<?php
namespace Mexbs\ApBase\Api\Data;

/**
 * Interface DiscountDetailsInterface
 * @api
 */
interface DiscountDetailsInterface
{
    /**
     * Get coupon code
     *
     * @return string
     */
    public function getCouponCode();

    /**
     * @param string $couponCode
     * @return $this
     */
    public function setCouponCode($couponCode);

    /**
     * Get description lines
     *
     * @return \Mexbs\ApBase\Api\Data\DescriptionLinesInterface[]
     */
    public function getDescriptionLines();

    /**
     * @param \Mexbs\ApBase\Api\Data\DescriptionLinesInterface[] $descriptionLines
     * @return $this
     */
    public function setDescriptionLines($descriptionLines);
}
