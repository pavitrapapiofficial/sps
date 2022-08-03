<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Api\Data;

interface RewardproductSpecificInterface
{
    const ENTITY_ID   = 'entity_id';
    const PRODUCT_ID  = 'product_id';
    const POINTS      = 'points';
    const START_TIME  = 'start_time';
    const END_TIME    = 'end_time';
    const STATUS      = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Get Product ID
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get Points
     *
     * @return int|null
     */
    public function getPoints();

    /**
     * Get Start Time
     *
     * @return string|null
     */
    public function getStartTime();

    /**
     * Get End Time
     *
     * @return string|null
     */
    public function getEndTime();

    /**
     * Get Order ID
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @return int|null
     */
    public function setEntityId($id);

    /**
     * Set Customer ID
     *
     * @return int|null
     */
    public function setProductId($productId);

    /**
     * Set Total Reward Point
     *
     * @return int|null
     */
    public function setPoints($point);

    /**
     * Set StartTime
     *
     * @return string|null
     */
    public function setStartTime($startTime);

    /**
     * Set EndTime
     *
     * @return string|null
     */
    public function setEndTime($endTime);

    /**
     * Set Status
     *
     * @return int|null
     */
    public function setStatus($status);
}
