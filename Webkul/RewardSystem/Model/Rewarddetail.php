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

namespace Webkul\RewardSystem\Model;

use Webkul\RewardSystem\Api\Data\RewarddetailInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Rewarddetail extends AbstractModel implements RewarddetailInterface, IdentityInterface
{
    const CACHE_TAG = 'rewardsystem_rewarddetail';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewardsystem_rewarddetail';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardsystem_rewarddetail';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\RewardSystem\Model\ResourceModel\Rewarddetail::class);
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getRewardPoint()
    {
        return $this->getData(self::REWARD_POINT);
    }

    public function setRewardPoint($point)
    {
        return $this->setData(self::REWARD_POINT, $point);
    }

    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getTransactionAt()
    {
        return $this->getData(self::TRANSACTION_AT);
    }

    public function setTransactionAt($date)
    {
        return $this->setData(self::TRANSACTION_AT, $date);
    }

    public function getCurrencyCode()
    {
        return $this->getData(self::CURRENCY_CODE);
    }

    public function setCurrencyCode($code)
    {
        return $this->setData(self::CURRENCY_CODE, $code);
    }

    public function getCurrAmount()
    {
        return $this->getData(self::CURR_AMOUNT);
    }

    public function setCurrAmount($amount)
    {
        return $this->setData(self::CURR_AMOUNT, $amount);
    }
    public function getTransactionNote()
    {
        return $this->getData(self::TRANSACTION_NOTE);
    }

    public function setTransactionNote($note)
    {
        return $this->setData(self::TRANSACTION_NOTE, $note);
    }
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEW_ID, $reviewId);
    }
    public function getIsRevert()
    {
        return $this->getData(self::IS_REVERT);
    }
    public function setIsRevert($isRevert)
    {
        return $this->setData(self::IS_REVERT, $isRevert);
    }
    public function getRewardUsed()
    {
        return $this->getData(self::REWARD_USED);
    }
    public function setRewardUsed($rewardUsed)
    {
        return $this->setData(self::REWARD_USED, $rewardUsed);
    }
    public function getIsExpired()
    {
        return $this->getData(self::IS_EXPIRED);
    }
    public function setIsExpired($isExpired)
    {
        return $this->setData(self::IS_EXPIRED, $isExpired);
    }
    public function getIsExpirationEmailSent()
    {
        return $this->getData(self::IS_EXPIRATION_EMAIL_SENT);
    }
    public function setIsExpirationEmailSent($isExpiredEmailSent)
    {
        return $this->setData(self::IS_EXPIRATION_EMAIL_SENT, $isExpiredEmailSent);
    }
    public function getExpiresAt()
    {
        return $this->getData(self::EXPIRES_AT);
    }
    public function setExpiresAt($expiresAt)
    {
        return $this->setData(self::EXPIRES_AT, $expiresAt);
    }
}
