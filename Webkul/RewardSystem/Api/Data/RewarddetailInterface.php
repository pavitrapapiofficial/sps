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

interface RewarddetailInterface
{
    const ENTITY_ID                 = 'entity_id';
    const CUSTOMER_ID               = 'customer_id';
    const REWARD_POINT              = 'reward_point';
    const AMOUNT                    = 'amount';
    const STATUS                    = 'status';
    const ACTION                    = 'action';
    const ORDER_ID                  = 'order_id';
    const TRANSACTION_AT            = 'transaction_at';
    const CURRENCY_CODE             = 'currency_code';
    const CURR_AMOUNT               = 'curr_amount';
    const TRANSACTION_NOTE          = 'transaction_note';
    const REVIEW_ID                 = 'review_id';
    const IS_REVERT                 = 'is_revert';
    const REWARD_USED               = 'reward_used';
    const IS_EXPIRED                = 'is_expired';
    const IS_EXPIRATION_EMAIL_SENT  = 'is_expiration_email_sent';
    const EXPIRES_AT                = 'expires_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Get Customer ID
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get Reward Point
     *
     * @return int|null
     */
    public function getRewardPoint();

    /**
     * Get Amount
     *
     * @return int|null
     */
    public function getAmount();

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Get Action
     *
     * @return int|null
     */
    public function getAction();

    /**
     * Get Order id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get Tranction Date
     *
     * @return int|null
     */
    public function getTransactionAt();

    /**
     * Get Currency Code
     *
     * @return int|null
     */
    public function getCurrencyCode();

    /**
     * Get Current Amount
     *
     * @return int|null
     */
    public function getCurrAmount();

    /**
     * Get Transaction Note
     *
     * @return int|null
     */
    public function getTransactionNote();

    /**
     * Get Review Id
     *
     * @return int|null
     */
    public function getReviewId();

    /**
     * Get Is Reward Revert
     *
     * @return int|null
     */
    public function getIsRevert();

    /**
     * Get REWARD USED
     *
     * @return float|null
     */
    public function getRewardUsed();

    /**
     * Get Is Expired
     *
     * @return int|null
     */
    public function getIsExpired();

    /**
     * Get Is Expiration Email Sent
     *
     * @return int|null
     */
    public function getIsExpirationEmailSent();

    /**
     * Get Expires Date
     *
     * @return string|null
     */
    public function getExpiresAt();

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
     * Set Reward Point
     *
     * @return int|null
     */
    public function setRewardPoint($point);

    /**
     * Set Amount
     *
     * @return int|null
     */
    public function setAmount($amount);

    /**
     * Set Status
     *
     * @return int|null
     */
    public function setStatus($status);

    /**
     * Set Action
     *
     * @return int|null
     */
    public function setAction($action);

    /**
     * Set Order Id
     *
     * @return int|null
     */
    public function setOrderId($orderId);

    /**
     * Set Tranction Date
     *
     * @return int|null
     */
    public function setTransactionAt($date);

    /**
     * Set Currency Code
     *
     * @return int|null
     */
    public function setCurrencyCode($code);

    /**
     * Set Current Amount
     *
     * @return int|null
     */
    public function setCurrAmount($amount);

    /**
     * Set Transaction Note
     *
     * @return int|null
     */
    public function setTransactionNote($note);
    /**
     * Set Review Id
     *
     * @return int|null
     */
    public function setReviewId($reviewId);

    /**
     * Set Is Reward Revert
     *
     * @return int|null
     */
    public function setIsRevert($isRevert);

    /**
     * Set REWARD USED
     *
     * @return float|null
     */
    public function setRewardUsed($rewardUsed);

    /**
     * Set Is Expired
     *
     * @return int|null
     */
    public function setIsExpired($isExpired);

    /**
     * Set Is Expiration Email Sent
     *
     * @return int|null
     */
    public function setIsExpirationEmailSent($isExpiredEmailSent);

    /**
     * Set Expires Date
     *
     * @return string|null
     */
    public function setExpiresAt($expiresAt);
}
