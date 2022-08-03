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

interface RewardrecordInterface
{
    const ENTITY_ID                 = 'entity_id';
    const CUSTOMER_ID               = 'customer_id';
    const TOTAL_REWARD_POINT        = 'total_reward_point';
    const REMAINING_REWARD_POINT    = 'remaining_reward_point';
    const USED_REWARD_POINT         = 'used_reward_point';
    const UPDATED_AT                = 'updated_at';

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
    public function getCustomerId();

    /**
     * Get Quote ID
     *
     * @return int|null
     */
    public function getTotalRewardPoint();

    /**
     * Get Order ID
     *
     * @return int|null
     */
    public function getRemainingRewardPoint();

    /**
     * Get Customer ID
     *
     * @return int|null
     */
    public function getUsedRewardPoint();

    /**
     * Get allowed order
     *
     * @return int|null
     */
    public function getUpdatedAt();

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
    public function setCustomerId($customerId);

    /**
     * Set Total Reward Point
     *
     * @return int|null
     */
    public function setTotalRewardPoint($point);

    /**
     * Set Remaining Reward Total
     *
     * @return int|null
     */
    public function setRemainingRewardPoint($point);

    /**
     * Set Used Reward Point
     *
     * @return int|null
     */
    public function setUsedRewardPoint($point);

    /**
     * Set Updated At
     *
     * @return int|null
     */
    public function setUpdatedAt($date);
}
