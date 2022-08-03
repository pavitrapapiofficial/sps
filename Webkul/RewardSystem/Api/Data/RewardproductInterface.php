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

interface RewardproductInterface
{
    const ENTITY_ID   = 'entity_id';
    const PRODUCT_ID  = 'product_id';
    const POINTS      = 'points';
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
    public function getProductId();

    /**
     * Get Quote ID
     *
     * @return int|null
     */
    public function getPoints();

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
     * Set Remaining Reward Total
     *
     * @return int|null
     */
    public function setStatus($status);
}