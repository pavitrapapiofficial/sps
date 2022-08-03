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

interface RewardcategorySpecificInterface
{
    const ENTITY_ID   = 'entity_id';
    const CATEGORY_ID = 'category_id';
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
     * Get Seller ID
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Get Quote ID
     *
     * @return int|null
     */
    public function getPoints();

    /**
     * Get StartTime
     *
     * @return string|null
     */
    public function getStartTime();

    /**
     * Get EndTime
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
    public function setCategoryId($categoryId);

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
     * Set Remaining Reward Total
     *
     * @return int|null
     */
    public function setStatus($status);
}
